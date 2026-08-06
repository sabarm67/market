<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import {
  createChart,
  CandlestickSeries,
  LineSeries,
  HistogramSeries,
  type IChartApi,
} from 'lightweight-charts'

interface Candle { date: string; open: number; high: number; low: number; close: number; volume: number }

const props = defineProps<{
  candles: Candle[]
  indicators: Record<string, any>
}>()

const priceEl = ref<HTMLDivElement | null>(null)
const volumeEl = ref<HTMLDivElement | null>(null)
const rsiEl = ref<HTMLDivElement | null>(null)
const macdEl = ref<HTMLDivElement | null>(null)

let charts: IChartApi[] = []

function chartOptions(height: number) {
  return {
    height,
    layout: { background: { color: 'transparent' }, textColor: '#94a3b8' },
    grid: { vertLines: { color: '#334155' }, horzLines: { color: '#334155' } },
    timeScale: { borderColor: '#475569' },
    rightPriceScale: { borderColor: '#475569' },
  }
}

function render() {
  charts.forEach((c) => c.remove())
  charts = []

  if (!props.candles.length || !priceEl.value) return

  const times = props.candles.map((c) => c.date)

  // Price chart: candles + MA + Bollinger Bands overlay
  const priceChart = createChart(priceEl.value, chartOptions(320))
  const candleSeries = priceChart.addSeries(CandlestickSeries, {
    upColor: '#16a34a', downColor: '#dc2626', borderVisible: false,
    wickUpColor: '#16a34a', wickDownColor: '#dc2626',
  })
  candleSeries.setData(props.candles.map((c) => ({ time: c.date, open: c.open, high: c.high, low: c.low, close: c.close })))

  if (props.indicators.ma) {
    const ma = priceChart.addSeries(LineSeries, { color: '#2f6fed', lineWidth: 1, title: 'MA(20)' })
    ma.setData(zip(times, props.indicators.ma))
  }
  if (props.indicators.bbands) {
    const upper = priceChart.addSeries(LineSeries, { color: '#94a3b8', lineWidth: 1, title: 'BB Upper' })
    upper.setData(zip(times, props.indicators.bbands.upper))
    const lower = priceChart.addSeries(LineSeries, { color: '#94a3b8', lineWidth: 1, title: 'BB Lower' })
    lower.setData(zip(times, props.indicators.bbands.lower))
  }
  priceChart.timeScale().fitContent()
  charts.push(priceChart)

  // Volume
  if (volumeEl.value) {
    const volumeChart = createChart(volumeEl.value, chartOptions(100))
    const volSeries = volumeChart.addSeries(HistogramSeries, { color: '#64748b' })
    volSeries.setData(props.candles.map((c) => ({ time: c.date, value: c.volume })))
    volumeChart.timeScale().fitContent()
    charts.push(volumeChart)
  }

  // RSI
  if (props.indicators.rsi && rsiEl.value) {
    const rsiChart = createChart(rsiEl.value, chartOptions(120))
    const rsiSeries = rsiChart.addSeries(LineSeries, { color: '#a855f7', lineWidth: 1, title: 'RSI(14)' })
    rsiSeries.setData(zip(times, props.indicators.rsi))
    rsiChart.timeScale().fitContent()
    charts.push(rsiChart)
  }

  // MACD
  if (props.indicators.macd && macdEl.value) {
    const macdChart = createChart(macdEl.value, chartOptions(120))
    const hist = macdChart.addSeries(HistogramSeries, { color: '#94a3b8', title: 'Histogram' })
    hist.setData(zip(times, props.indicators.macd.histogram))
    const macdLine = macdChart.addSeries(LineSeries, { color: '#2f6fed', lineWidth: 1, title: 'MACD' })
    macdLine.setData(zip(times, props.indicators.macd.macd))
    const signalLine = macdChart.addSeries(LineSeries, { color: '#f59e0b', lineWidth: 1, title: 'Signal' })
    signalLine.setData(zip(times, props.indicators.macd.signal))
    macdChart.timeScale().fitContent()
    charts.push(macdChart)
  }
}

function zip(times: string[], values: (number | null)[]) {
  return times
    .map((time, i) => ({ time, value: values[i] }))
    .filter((d): d is { time: string; value: number } => d.value !== null && d.value !== undefined)
}

onMounted(render)
watch(() => [props.candles, props.indicators], render)
onBeforeUnmount(() => charts.forEach((c) => c.remove()))
</script>

<template>
  <div class="space-y-2">
    <div ref="priceEl"></div>
    <div ref="volumeEl"></div>
    <div v-if="indicators.rsi" ref="rsiEl"></div>
    <div v-if="indicators.macd" ref="macdEl"></div>
  </div>
</template>
