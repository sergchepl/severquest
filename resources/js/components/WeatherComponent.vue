<template>
  <div class="col-md-4 mb">
    <div class="weather pn">
      <i class="fa fa-cloud fa-4x"></i>
      <h2>{{ temperature }}º C</h2>
      <h4>СЕВЕРОДОНЕЦК</h4>
    </div>
  </div>
</template>

<script>
export default {
    props: [],
    data: function() {
        return {
            weather: {},
        }
    },
    computed: {
        temperature() {
            return +this.weather.main.temp - 273.15;
        }
    },
    mounted() {
        window.axios.get('https://api.openweathermap.org/data/2.5/weather?q=SIEVIERODONETSK&appid=8ec388c04f920dbec4234d96e9be6623')
            .then(function(response) {
                console.log(response);
                this.weather = response;
            });
    },
    methods: {
        
    },
};
</script>

<style>
</style>
