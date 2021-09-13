<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context;

use BehatMessengerContext\Context\Traits\ArraySimilarTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MessengerContext implements Context
{
    use ArraySimilarTrait;

    private ContainerInterface $container;
    private NormalizerInterface $normalizer;
    /** @var array<string, string> */
    private array $placeholderPatternMap;

    /**
     * @param ContainerInterface $container
     * @param NormalizerInterface $normalizer
     * @param array<string, string> $placeholderPatternMap
     */
    public function __construct(
        ContainerInterface $container,
        NormalizerInterface $normalizer,
        array $placeholderPatternMap = []
    ) {
        $this->container = $container;
        $this->normalizer = $normalizer;
        $this->placeholderPatternMap = $placeholderPatternMap;
    }

    /**
     * @Then transport :transportName should contain message with JSON:
     */
    public function transportShouldContainMessageWithJson(string $transportName, PyStringNode $expectedMessage): void
    {
        $expectedMessage = $this->decodeExpectedJson($expectedMessage);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessage = $this->convertToArray($envelope->getMessage());
            if ($this->isArraysSimilar($expectedMessage, $actualMessage)) {
                return;
            }

            $actualMessageList[] = $actualMessage;
        }

        throw new Exception(
            sprintf(
                'The transport doesn\'t contain message with such JSON, actual messages: %s',
                $this->getPrettyJson($actualMessageList)
            )
        );
    }

    /**
     * @Then transport :transportName should contain message with JSON and variable fields :variableFields:
     */
    public function transportShouldContainMessageWithJsonAndVariableFields(
        string $transportName,
        string $variableFields,
        PyStringNode $expectedMessage
    ): void {
        $variableFields = $variableFields ? array_map('trim', explode(',', $variableFields)) : [];
        $expectedMessage = $this->decodeExpectedJson($expectedMessage);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessage = $this->convertToArray($envelope->getMessage());
            $isArraysSimilar = $this->isArraysSimilar(
                $expectedMessage,
                $actualMessage,
                $variableFields,
                $this->placeholderPatternMap
            );
            if ($isArraysSimilar) {
                return;
            }

            $actualMessageList[] = $actualMessage;
        }

        throw new Exception(
            sprintf(
                'The transport doesn\'t contain message with such JSON, actual messages: %s',
                $this->getPrettyJson($actualMessageList)
            )
        );
    }

    /**
     * @Then all transport :transportName messages should be JSON:
     */
    public function allTransportMessagesShouldBeJson(string $transportName, PyStringNode $expectedMessageList): void
    {
        $expectedMessageList = $this->decodeExpectedJson($expectedMessageList);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessageList[] = $this->convertToArray($envelope->getMessage());
        }

        if (!$this->isArraysSimilar($expectedMessageList, $actualMessageList)) {
            throw new Exception(
                sprintf(
                    'The expected transport messages doesn\'t match actual: %s',
                    $this->getPrettyJson($actualMessageList)
                )
            );
        }
    }

    /**
     * @Then all transport :transportName messages should be JSON with variable fields :variableFields:
     */
    public function allTransportMessagesShouldBeJsonWithVariableFields(
        string $transportName,
        string $variableFields,
        PyStringNode $expectedMessageList
    ): void {
        $variableFields = $variableFields ? array_map('trim', explode(',', $variableFields)) : [];
        $expectedMessageList = $this->decodeExpectedJson($expectedMessageList);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessageList[] = $this->convertToArray($envelope->getMessage());
        }

        $isArraysSimilar = $this->isArraysSimilar(
            $expectedMessageList,
            $actualMessageList,
            $variableFields,
            $this->placeholderPatternMap
        );
        if (!$isArraysSimilar) {
            throw new Exception(
                sprintf(
                    'The expected transport messages doesn\'t match actual: %s',
                    $this->getPrettyJson($actualMessageList)
                )
            );
        }
    }

    /**
     * @Then there is :expectationMessageCount messages in transport :transportName
     */
    public function thereIsCountMessagesInTransport(int $expectedMessageCount, string $transportName): void
    {
        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageCount = count($transport->get());

        if ($actualMessageCount !== $expectedMessageCount) {
            throw new Exception(
                sprintf(
                    'In transport exist actual count: %s, but expected count: %s',
                    $actualMessageCount,
                    $expectedMessageCount
                )
            );
        }
    }

    /**
     * @param array<mixed> $message
     * @return string|bool
     */
    private function getPrettyJson(array $message)
    {
        return json_encode($message, JSON_PRETTY_PRINT);
    }

    /**
     * @param mixed $object
     * @return array<mixed>
     */
    private function convertToArray($object): array
    {
        return (array) $this->normalizer->normalize($object);
    }

    /**
     * @return array<mixed>
     */
    private function decodeExpectedJson(PyStringNode $expectedJson): array
    {
        return json_decode(
            trim($expectedJson->getRaw()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    private function getMessengerTransportByName(string $transportName): InMemoryTransport
    {
        $fullName = 'messenger.transport.' . $transportName;
        $hasTransport = $this->container->has($fullName);

        if ($hasTransport === false) {
            throw new Exception('Transport ' . $fullName . ' not found');
        }

        $transport = $this->container->get($fullName);

        if ($transport instanceof InMemoryTransport) {
            return $transport;
        }

        throw new Exception(
            'In memory transport ' . $fullName . ' not found'
        );
    }
}
