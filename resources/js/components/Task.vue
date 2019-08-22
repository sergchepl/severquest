<template>
  <transition name="color">
    <div class="card mt-2" :class="[statusClass, {'sharing': task.type == 2}, {'banned': isBanned && +task.type != 2}, {'wibro': isDoubleTask}]">
            <div class="card-header" :id="'heading-'+task.id">
                <h5 class="mb-0">
                    <a data-toggle="collapse" :href="'#collapse-'+task.id" aria-expanded="false">
                        <span v-if="task.type == 1" class="dot" :class="[statusClass, {'banned': isBanned && +task.type != 2}]"></span> {{ task.name }}
                    </a>
                    <span class="badge badge-light">{{ task.score }}</span>
                </h5>
            </div>
        
            <div :id="'collapse-'+task.id" class="collapse" :aria-labelledby="'heading-'+task.id">
                <div class="card-body">
                    <p v-html="task.description"></p>
                    <template v-if="!status">
                        <button v-if="!+task.user_id && +task.type != 2" class="btn btn-coral" role="button" @click="takeTask">За дело!</button>
                        <button v-if="+task.user_id == user.id && +task.type != 2" class="btn btn-cancel hide" role="button" @click="cancelTask">Отменись!</button>
                        <button v-if="+task.user_id == user.id || +task.type == 2" :class="+task.type == 2 ? 'btn-cancel' : 'btn-success'" class="btn" role="button" @click="sendAnswer">Хочу Сдать!</button>
                    </template>
                    <p v-else class="status">{{ status }}</p>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    props: ['taskProp', 'user', 'isBannedProp'],
    data: function() {
        return {
            task: this.taskProp,
            isBanned: this.isBannedProp,
            isDoubleTask: false,
        }
    },
    mounted() {
        this.taskChannel.listen('TaskUpdate', ({task}) => {
            if (task.id == this.task.id) {
                this.task.user_id = task.user_id;
                this.task.status = task.status;
            }
        });
        this.taskChannel.listen('BanUpdate', ({ban, active}) => {
            console.log(ban, active);
            if (ban.task_id == this.task.id && ban.user_id == this.user.id && active) {
                this.isBanned = true;
            }
            if (ban.task_id == this.task.id && ban.user_id == this.user.id && !active) {
                this.isBanned = false;
            }
        });
    },
    computed: {
        taskChannel() {
            return window.Echo.channel('tasks'); // will listen all task events
        },
        status() {
            let status = +this.task.status;

            if (this.isBanned && +this.task.type == 2) {
                return 'Ваш ответ принят!';
            }
            if (this.isBanned) {
                return 'Задание заблокировано для выполнения!';
            }
            if (status == 0 || (status == 1 && this.user.id == this.task.user_id)) {
                return '';
            }
            if (this.user.id != this.task.user_id) {
                return 'Уже занято другой командой!';
            }
            if (this.user.id == this.task.user_id && status == 2) {
                return 'Находится на проверке, ожидайте!';
            }
            if (this.user.id == this.task.user_id && status == 3) {
                return 'Успешно выполнено!';
            }
        },
        statusClass() {
            let status = this.task.status;

            if ((this.user.id == this.task.user_id || this.task.user_id == 0) && !this.isBanned) {
                switch(+status) {
                    case 0:
                        return '';
                    case 1:
                        return 'inwork';
                    case 2:
                        return 'check';
                    case 3:
                        return 'done';
                }
            }
            if (+this.task.type == 2 && this.isBanned) {
                return 'disabled check';
            }
            return 'disabled';
        }
    },
    methods: {
        takeTask() {
            axios.put('api/v1/task/'+ this.task.id + '/take')
            .catch(error => {
                console.log(error.response);
                if (error.response.status == 409) {
                    this.isDoubleTask = true;
                    setTimeout(() => this.isDoubleTask = false, 500);
                }
            })
        },
        cancelTask() {
            axios.put('api/v1/task/'+ this.task.id + '/cancel')
            .catch(error => {
                console.log(error.response);
            })
        },
        sendAnswer() {
            this.$store.commit('setModal', {
                active: true,
                taskId: this.task.id
            });
        }
    }
}
</script>

<style>
.color-enter-active, .color-leave-active {
  transition: all .5s;
}
.wibro {
    -webkit-animation: 0.1s tremor ease-out infinite;  
    animation: 0.1s tremor ease-out infinite;
}
@-webkit-keyframes tremor {
    0%, 25% {
        left: -1px;
        top:-1px;
        -webkit-transform: translateX(-1px);
        transform: translateX(-1px);
    }
    50%, 100% {
        left: 1px;
        top: 1px;
        -webkit-transform: translateX(1px);
        transform: translateX(1px);
    }
}
@keyframes tremor {
    0%, 25% {
        left: -1px;
        -webkit-transform: translateX(-1px);
        transform: translateX(-1px);
    }
    50%, 100% {
        left: 1px;
        -webkit-transform: translateX(1px);
        transform: translateX(1px);
    }
}
</style>
