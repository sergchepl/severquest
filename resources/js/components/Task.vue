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
            this.task.user_id = task.user_id;
            this.task.status = task.status;

            if (task.user_id == 0) {
                this.sendNotification({'text': 'Задание <b>'+this.task.name+'</b> снова доступно!'});
            }
            if (this.user.id == task.user_id) {
                if (task.status == 3) {
                    this.sendNotification({'type': 'success', 'title': 'Поздравляем!', 'text': 'Задание <b>'+this.task.name+'</b> успешно выполнено!'});
                } else {
                    this.sendNotification({'text': 'Изменен статус задания <b>'+this.task.name+'</b>!'});
                }
            }
            if (this.user.id != task.user_id && task.status == 1) {
                this.sendNotification({'type': 'error', 'text': 'Задание <b>'+this.task.name+'</b> взято другой командой!'});
            }
        });
        this.taskChannel.listen('BanUpdate', ({ban, active}) => {
            if (ban.user_id == this.user.id) {
                if (active) {
                    this.isBanned = true;

                    const notificationStatus = 'error';
                    const titleForNotification = 'Задание Заблокировано';
                    const textForNotification = 'Задание <b>' + this.task.name + '</b> заблокировано для выполнения!';
                } else {
                    this.isBanned = false;

                    const notificationStatus = 'success';
                    const titleForNotification = 'Задание Разблокировано';
                    const textForNotification = 'Задание <b>' + this.task.name + '</b> снова доступно для выполнения!';
                }

                this.sendNotification({'type': 'error', 'title': titleForNotification, 'text': textForNotification});
            }

        });
    },
    computed: {
        taskChannel() {
            return window.Echo.channel('task.'+this.task.id); // will listen all task events
        },
        status() {
            const status = +this.task.status;

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
            const status = this.task.status;

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
                if (error.response.status == 403) {
                    this.isDoubleTask = true;
                    setTimeout(() => this.isDoubleTask = false, 500);
                    
                    this.sendNotification({'type': 'error', 'title': 'Ошибка!', 'text': 'Вы уже выполняете другое задание!'});
                }
                if (error.response.status == 409) {
                    window.location.href = error.response.data;
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
        },
        sendNotification({type = 'warn', title = 'Обновление', text}) {
            return this.$notify({
                group: 'info',
                type: type,
                title: title,
                text: text
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
