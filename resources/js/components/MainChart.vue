<template>
    <bar-chart :chart-data="chartData"></bar-chart>
</template>

<script>
import BarChart from '../charts/BarChart'

export default {
    props: ['users'],
    data: function() {
        return {
            teams: this.users,
        }
    },
    computed: {
        chartData() {
            const teamNames = this.teams.map(team => team.name);
            const teamScores = this.teams.map(team => team.score);

            return {
                labels: teamNames,
                datasets: [{
                    label: 'СТАТИСТИКА КОМАНД',
                    backgroundColor: '#4ecdc4',
                    data: teamScores,
                }],
            }
        }
    },
    mounted() {
        this.teams.forEach((team, teamId) => window.Echo.channel('score.'+ team.id)
            .listen('ScoreUpdate', (data) => {
                this.teams[teamId].score = data.score;
            })
        );
    },
    methods: {
        
    },
    components: {
        BarChart,
    }
}
</script>
