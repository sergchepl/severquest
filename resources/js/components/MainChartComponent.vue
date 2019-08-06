<template>
  <div><!--CUSTOM CHART START -->
    <div class="border-head">
      <h3>СТАТИСТИКА КОМАНД</h3>
    </div>
    <div class="custom-bar-chart">
      <ul class="y-axis">
        <li><span>250</span></li>
        <li><span>200</span></li>
        <li><span>150</span></li>
        <li><span>100</span></li>
        <li><span>50</span></li>
        <li><span>0</span></li>
      </ul>
      <div v-for="user in users" :key="user.id" class="bar">
        <div class="title">{{ user.name }}</div>
        <div class="value tooltips" :data-original-title="user.score" data-toggle="tooltip" data-placement="top">{{ user.score / 250 * 100 }}%</div>
      </div>
    </div>
    <!--custom chart end-->
    </div>
</template>

<script>
export default {
    props: ['users'],
    data: function() {
        return {
            
        }
    },
    computed: {
        
    },
    mounted() {
        
    },
    methods: {
        
    },
}
</script>

<style>

</style>
