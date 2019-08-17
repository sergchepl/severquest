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
            return window.Echo.private('score.' + this.user.id);
        },
    },
    mounted() {
        this.channel
            .listen('Score', ( data ) => {
                console.log(data);
                this.score = data.score;
            });
    },
    methods: {

    },
    
}
</script>

<style>

</style>
