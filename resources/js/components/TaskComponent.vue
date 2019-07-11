<template>
  <div class="card mt-2" :class="[statusClass, {'sharing': task.type == 2}, {'banned': isBanned}]">
        <div class="card-header" :id="'heading-'+task.id">
            <h5 class="mb-0">
                <a data-toggle="collapse" :href="'#collapse-'+task.id" aria-expanded="false">
                    {{ task.name }}
                </a>
                <span class="badge badge-light">{{ task.score }}</span>
            </h5>
        </div>
    
        <div :id="'collapse-'+task.id" class="collapse" :aria-labelledby="'heading-'+task.id">
            <div class="card-body">
                <p v-html="task.description"></p>
                <template v-if="!status">
                    <button v-if="!+task.user_id && +task.type != 2" class="btn btn-coral btn-lg" role="button" @click="takeTask">За дело!</button>
                    <button v-if="+task.user_id == user.id && +task.type != 2" class="btn btn-danger hide btn-lg" role="button" @click="cancelTask">Отменись!</button>
                    <button v-if="+task.user_id == user.id || +task.type == 2" :class="+task.type == 2 ? 'btn-info' : 'btn-success'" class="btn btn-lg" role="button" @click="sendAnswer">Хочу Сдать!</button>
                </template>
                <p v-else class="status">{{ status }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['taskProp', 'user'],
    data: function() {
        return {
            task: this.taskProp,
            isBanned: false,
        }
    },
    mounted() {
        window.taskChannel.listen('TaskUpdate', ({task}) => {
            if (task.id == this.task.id) {
                this.task.user_id = task.user_id;
                this.task.status = task.status;
            }
        });
        window.taskChannel.listen('BanUpdate', ({ban, active}) => {
            if (ban.task_id == this.task.id && ban.user_id == this.user.id && active) {
                this.isBanned = true;
            }
            if (ban.task_id == this.task.id && ban.user_id == this.user.id && !active) {
                this.isBanned = false;
            }
        });
    },
    computed: {
        status() {
            let status = +this.task.status;

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

            if (this.user.id == this.task.user_id || this.task.user_id == 0) {
                switch(+status) {
                    case 0:
                        return '';
                    case 1:
                        return 'inwork';
                    case 2:
                        return 'disabled check';
                    case 3:
                        return 'disabled done';
                }
            } else {
                return 'disabled';
            }
        }
    },
    methods: {
        takeTask() {
            axios.put('/take-task', {
                task_id: this.task.id,
                is_taking: true
            }).catch(error => {
                console.log(error.response);
                if (error.response.status == 409) {
                    alert('Вы можете выполнять только 1 задание одновременно!');
                }
            })
        },
        cancelTask() {
            axios.put('/take-task', {
                task_id: this.task.id,
                is_taking: false
            }).catch(error => {
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

</style>
