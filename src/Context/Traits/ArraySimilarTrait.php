<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context\Traits;

/**
 * copied from https://github.com/MacPaw/similar-arrays/blob/develop/src/SimilarArray.php and used as trait
 * modified to kollex needs:
 * - added $placeholderPatternMap
 * - changed preg_match delimiter from | to /
 */
trait ArraySimilarTrait
{
    /**
     * @param array<mixed>  $expected
     * @param array<mixed>  $actual
     * @param array<string> $regexVariableKeys
     * @param array<string, string> $placeholderPatternMap
     */
    protected function isArraysSimilar(
        array $expected,
        array $actual,
        array $regexVariableKeys = [],
        array $placeholderPatternMap = []
    ): bool {
        if (array_keys($expected) !== array_keys($actual)) {
            return false;
        }

        foreach ($expected as $key => $value) {
            if (!isset($actual[$key]) && $value !== null) {
                return false;
            }

            if (!in_array($key, $regexVariableKeys, true) && gettype($value) !== gettype($actual[$key])) {
                return false;
            }

            if (!is_array($value)) {
                if ($value !== $actual[$key] && !in_array($key, $regexVariableKeys, true)) {
                    return false;
                }

                if (!in_array($key, $regexVariableKeys, true)) {
                    continue;
                }

                if (!is_string($value)) {
                    return false;
                }

                $isPlaceholder = !empty($placeholderPatternMap)
                    && strpos($value, '{') === 0
                    && \substr($value, -1) === '}';

                if (strpos($value, '~') !== 0 && !$isPlaceholder) {
                    return false;
                }

                if ($isPlaceholder) {
                    $placeholder = \str_replace(['{', '}'], '', $value);
                    $pattern = $placeholderPatternMap[$placeholder];
                } else {
                    $pattern = sprintf('/%s/', substr($value, 1));
                }

                $pregMatchValue = preg_match(
                    $pattern,
                    sprintf('%s', $actual[$key])
                );
                if ($pregMatchValue === 0 || $pregMatchValue === false) {
                    return false;
                }
            }

            if (
                is_array($value)
                && is_array($actual[$key])
                && !$this->isArraysSimilar($value, $actual[$key], $regexVariableKeys, $placeholderPatternMap)
            ) {
                return false;
            }
        }

        return true;
    }
}
