<template>
    <span class="badge badge-light">{{ score }}</span>
</template>

<script>
export default {
    props: ['user'],
    data: function() {
        return {
            score: this.user.score,
        }
    },
    computed: {
        channel() {
            return window.Echo.channel('score.' + this.user.id);
        },
    },
    mounted() {
        this.channel
            .listen('ScoreUpdate', ( data ) => {
                this.score = data.score;
            });
    },
    methods: {

    },
    
}
</script>

<style>

</style>
