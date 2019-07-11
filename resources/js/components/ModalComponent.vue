<template>
  <div class="answer" v-if="modal.active">
        <button type="button" class="close">
            <span aria-hidden="true">&times;</span>
        </button>
        <form ref="form" class="form-inline" method="post" enctype="multipart/form-data">
            <progress v-if="uploading" class="progress" :value="progress" max="100">{{ progress }}%</progress>
            <input type="hidden" name="task_id" :value="modal.taskId">
            <div class="form-group ">
                <input type="file" name="files[]" style="font-size: 1rem" multiple>
            </div>
            <div class="form-group mr-3 mb-2">
                <input type="text" class="form-control" name="text" placeholder="Доп.текст">
            </div>
            <button @click.prevent="send" style="font-size: 1rem" class="btn btn-primary mb-2">Submit</button>
        </form>
    </div>
</template>

<script>
export default {
    props: [],
    data: function() {
        return {
            uploading: false,
            progress: 0,
        }
    },
    computed: {
        modal() {
            return this.$store.state.modal;
        }
    },
    mounted() {
        
    },
    methods: {
        send() {
            this.uploading = true;

            const formData = new FormData($(this.$refs.form).get(0));

            window.axios.post('/send-answer', formData, {
                onUploadProgress: e => this.progress = Math.round(e.loaded * 100 / e.total),
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.uploading = false;
                this.$store.commit('setModal', {
                    active: false,
                    taskId: null
                });
            }).catch(error => {
                console.log(error.perponse);
            });
        }
    },
    
}
</script>

<style>

</style>
