<?php

namespace App\Services;

/**
 * Basic technical indicator calculations for FR-TECH-2 to FR-TECH-5 (MA, Bollinger, RSI, MACD).
 * Deliberately simple (no external TA library) — matches the "basic technical analysis"
 * MVP scope in functional-requirements-specification.md, not the full indicator library
 * deferred to the roadmap.
 */
class TechnicalIndicators
{
    /** Simple Moving Average. */
    public static function sma(array $closes, int $period): array
    {
        $result = [];
        $count = count($closes);
        for ($i = 0; $i < $count; $i++) {
            if ($i < $period - 1) {
                $result[] = null;
                continue;
            }
            $slice = array_slice($closes, $i - $period + 1, $period);
            $result[] = round(array_sum($slice) / $period, 4);
        }

        return $result;
    }

    /** Bollinger Bands: [upper, middle, lower]. */
    public static function bollingerBands(array $closes, int $period = 20, float $stdDevMultiplier = 2.0): array
    {
        $middle = self::sma($closes, $period);
        $upper = [];
        $lower = [];
        $count = count($closes);

        for ($i = 0; $i < $count; $i++) {
            if ($i < $period - 1) {
                $upper[] = null;
                $lower[] = null;
                continue;
            }
            $slice = array_slice($closes, $i - $period + 1, $period);
            $mean = $middle[$i];
            $variance = array_sum(array_map(fn ($v) => ($v - $mean) ** 2, $slice)) / $period;
            $stdDev = sqrt($variance);
            $upper[] = round($mean + $stdDevMultiplier * $stdDev, 4);
            $lower[] = round($mean - $stdDevMultiplier * $stdDev, 4);
        }

        return ['upper' => $upper, 'middle' => $middle, 'lower' => $lower];
    }

    /** Relative Strength Index. */
    public static function rsi(array $closes, int $period = 14): array
    {
        $count = count($closes);
        $result = array_fill(0, $count, null);

        if ($count <= $period) {
            return $result;
        }

        $gains = [];
        $losses = [];
        for ($i = 1; $i < $count; $i++) {
            $change = $closes[$i] - $closes[$i - 1];
            $gains[$i] = max($change, 0);
            $losses[$i] = max(-$change, 0);
        }

        $avgGain = array_sum(array_slice($gains, 1, $period, true)) / $period;
        $avgLoss = array_sum(array_slice($losses, 1, $period, true)) / $period;

        for ($i = $period + 1; $i < $count; $i++) {
            $avgGain = (($avgGain * ($period - 1)) + $gains[$i]) / $period;
            $avgLoss = (($avgLoss * ($period - 1)) + $losses[$i]) / $period;

            $rs = $avgLoss == 0 ? 100 : $avgGain / $avgLoss;
            $result[$i] = $avgLoss == 0 ? 100.0 : round(100 - (100 / (1 + $rs)), 2);
        }

        return $result;
    }

    /** Exponential Moving Average, aligned to input index (null before it has `period` points). */
    public static function ema(array $closes, int $period): array
    {
        $count = count($closes);
        $result = array_fill(0, $count, null);
        if ($count < $period) {
            return $result;
        }

        $multiplier = 2 / ($period + 1);
        $sma = array_sum(array_slice($closes, 0, $period)) / $period;
        $result[$period - 1] = round($sma, 4);

        for ($i = $period; $i < $count; $i++) {
            $result[$i] = round((($closes[$i] - $result[$i - 1]) * $multiplier) + $result[$i - 1], 4);
        }

        return $result;
    }

    /** MACD: [macd, signal, histogram]. */
    public static function macd(array $closes, int $fast = 12, int $slow = 26, int $signalPeriod = 9): array
    {
        $emaFast = self::ema($closes, $fast);
        $emaSlow = self::ema($closes, $slow);
        $count = count($closes);

        $macdLine = [];
        for ($i = 0; $i < $count; $i++) {
            $macdLine[] = ($emaFast[$i] !== null && $emaSlow[$i] !== null)
                ? round($emaFast[$i] - $emaSlow[$i], 4)
                : null;
        }

        $macdValues = array_values(array_filter($macdLine, fn ($v) => $v !== null));
        $signalValuesRaw = self::ema($macdValues, $signalPeriod);

        $signalLine = array_fill(0, $count, null);
        $firstMacdIndex = array_search(null, $macdLine, true) === 0 ? null : null;
        $offset = 0;
        foreach ($macdLine as $i => $v) {
            if ($v === null) {
                continue;
            }
            $signalLine[$i] = $signalValuesRaw[$offset] ?? null;
            $offset++;
        }

        $histogram = [];
        for ($i = 0; $i < $count; $i++) {
            $histogram[] = ($macdLine[$i] !== null && $signalLine[$i] !== null)
                ? round($macdLine[$i] - $signalLine[$i], 4)
                : null;
        }

        return ['macd' => $macdLine, 'signal' => $signalLine, 'histogram' => $histogram];
    }
}
