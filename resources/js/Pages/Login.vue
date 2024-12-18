<template>
    <div class="h-screen bg-[#f3f4f6]" style="  display: flex; justify-content: center; align-items: center">
        <div class="rounded"
            style="height: 80%; width: 90%; display: flex; justify-content: center;align-items: center;">
            <div style="width: 99%; height: 99%;">
                <a-row style="height:500px; padding:0;margin: 0">
                    <a-col :span="12" class="bg-white">
                        <div style="height: 250px; display: flex; justify-content: center;align-items: center;">
                            <div
                                style="background-image: url('gcicon.png'); background-size: cover; height: 200px; width: 200px;">
                            </div>
                        </div>
                        <div style="height: 300px; display: flex; justify-content: center; align-items: center;">
                            <div style="height: 250px;  width: 400px;">
                                <a-form-item label="Username" has-feedback :help="form.errors.username"
                                    :validate-status="form.errors.username ? 'error' : ''
                                        ">
                                    <a-input @keyup.enter="login" v-model:value="form.username"
                                        @change="form.errors.username = ''" />
                                </a-form-item>
                                <a-form-item label="Password" has-feedback :help="form.errors.password"
                                    :validate-status="form.errors.password ? 'error' : ''
                                        ">
                                    <a-input @keyup.enter="login" type="password" v-model:value="form.password"
                                        @change="form.errors.password = ''" />
                                </a-form-item>
                                <div>
                                    <div class="flex justify-center">
                                        <a-button :loading="loading" @click="login" style="width: 200px;"
                                            type="primary">Login</a-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a-col>
                    <a-col class="bg-blue-100" :span="12">
                        <div
                            style="background-image: url('background.png'); background-size: cover; background-repeat: no-repeat; height: 100%;">
                        </div>
                    </a-col>
                </a-row>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';


const loading = ref(false);

const form = useForm({
    username: '',
    password: '',
});

const login = () => {
    form.post(route('login'), {
        onStart: () => {
            loading.value = true
        },
        onSuccess: () => {
            form.reset()
        },
        onError: () => {
            loading.value = false
        }
    })
}

</script>