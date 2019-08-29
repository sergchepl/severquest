import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
  extends: Bar,
  mixins: [reactiveProp],
  props: ['options'],
  mounted () {
    this.renderChart(this.chartData, {
      maintainAspectRatio: false,
      responsiveAnimationDuration: 5,
      scales: {
        xAxes: [{
          barPercentage	: 0.5,
          minBarLength: 2,
        }]
      },
    });
  }
}