<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form: any = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <a-row :gutter="[16, 16]" style="background-image: url('/bg-circuit.png');">
        <a-col :span="12">
            <div class="flex justify-center items-center" style="height: 90vh; margin-top: 30px;">
                <img src="/gcicon.png" alt="logoimage">
            </div>
        </a-col>
        <a-col :span="12">
            <body>
                <div class="container">
                    <div class="login-box">
                        <h2>Login</h2>
                        <form action="#" @submit.prevent="submit">
                            <div class="input-box">
                                <input name="username" 
                                       v-model="form.username" 
                                       type="text" 
                                       required>
                                <label>Username</label>
                                <InputError class="mt-2" :message="form.errors.username" />
                            </div>
                            <div class="input-box">
                                <input name="password" 
                                       v-model="form.password" 
                                       type="password" 
                                       required>
                                <label>Password</label>
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>
                            <button type="submit" class="btn">Login</button>
                        </form>
                    </div>
                </div>
            </body>
        </a-col>
    </a-row>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    position: relative;
    width: 400px;
    height: 400px;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow:  0px 0px 5px 2px floralwhite;
    border-radius: 10px;
}

.container span {
    position: absolute;
    left: 0;
    width: 32px;
    height: 6px;
    background: #2c4766;
    border-radius: 8px;
    transform-origin: 128px;
    transform: scale(2.2) rotate(calc(var(--i) * (360deg / 50)));
    animation: animateBlink 3s linear infinite;
    animation-delay: calc(var(--i) * (3s / 50));
}

@keyframes animateBlink {
    0% {
        background: #0ef;
    }

    25% {
        background: #2c4766;
    }
}

.login-box {
    position: absolute;
    width: 400px;
}

.login-box form {
    width: 100%;
    padding: 0 50px;
}

h2 {
    font-size: 2em;
    color: #0ef;
    text-align: center;
}

.input-box {
    position: relative;
    margin: 25px 0;
}

.input-box input {
    width: 100%;
    height: 50px;
    background: transparent;
    border: 2px solid #2c4766;
    outline: none;
    border-radius: 40px;
    font-size: 1em;
    color: #fff;
    padding: 0 20px;
    transition: .5s ease;
}

.input-box input:focus {
    border-color: #0ef;
}

.input-box label {
    position: absolute;
    top: -10px; /* Position the label at the top */
    left: 20px;
    font-size: .8em; /* Smaller font size */
    background: #1f293a;
    padding: 0 6px;
    color: #0ef;
    transition: .5s ease;
    border-radius: 5px;
}



.btn {
    width: 100%;
    height: 45px;
    background: #0ef;
    border: none;
    outline: none;
    border-radius: 40px;
    cursor: pointer;
    font-size: 1em;
    color: #1f293a;
    font-weight: 600;
}



p {
    color: #fff;
    font-size: .75rem;
}
</style>
