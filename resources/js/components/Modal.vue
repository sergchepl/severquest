<template>
  <transition name="slide-fade">
    <div class="answer" v-if="modal.active">
            <button type="button" class="close" @click="closeModal">
                <span aria-hidden="true">&times;</span>
            </button>
            <form ref="form" class="form-inline align-items-center w-100" method="post" enctype="multipart/form-data">
                <input type="hidden" name="task_id" :value="modal.taskId">
                <div class="w-100 d-flex justify-content-around align-items-center mb-2">
                    <input id="file" type="file" name="files[]" style="font-size: 1rem" class="inputfile" multiple @change="changeFile">
                    <label for="file">
                        <figure>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path>
                            </svg>
                        </figure> 
                    <span>{{ inputText }}</span>
                    </label>
                    <div class="progress w-50">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" :style="'width:' + progress + '%'" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="w-100 d-flex justify-content-around align-items-center p-3">
                    <input type="text" class="form-control" name="text" placeholder="Доп.текст">
                    <button @click.prevent="send" style="font-size: 1rem" class="btn btn-success ml-3">Отправить</button>
                </div>
            </form>
        </div>
    </transition>
</template>

<script>
export default {
    props: [],
    data: function() {
        return {
            uploading: false,
            progress: 0,
            inputText: 'Выбрать файлы'
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
        refreshData() {
            this.uploading = false;
            this.progress = 0;
            this.inputText = 'Выбрать файлы';
        },
        closeModal() {
            this.$store.commit('setModal', {
                    active: false,
                    taskId: null
                });
        },
        send() {
            this.uploading = true;

            const formData = new FormData($(this.$refs.form).get(0));

            window.axios.post('/send-answer', formData, {
                onUploadProgress: e => this.progress = Math.round(e.loaded * 100 / e.total),
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.refreshData();
                this.$store.commit('setModal', {
                    active: false,
                    taskId: null
                });
            }).catch(error => {
                console.log(error.response);
            });
        },
        changeFile(e) {
            if (e.target.files.length > 0) {
                this.inputText = "Выбрано "+e.target.files.length + ' фото';
            } else {
                this.inputText = 'Выбрать файлы';
            }
        }
    },
    
}
</script>

<style>
.inputfile {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}
.inputfile + label {
    color: white;
    font-size: 1.25rem;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    overflow: hidden;
    padding: 0;
    margin: 0;
    display: flex;
    flex-flow: column;
    align-items: center;
}
.inputfile + label figure {
    background-color: white;
    display: block;
    position: relative;
    padding: 10px;
    margin: 0;
    border-radius: 10%;
    flex-grow: 1;
}
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .3s ease;
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active до версии 2.1.8 */ {
  transform: translateY(100px);
  opacity: 0;
}
</style>
