<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
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

<style>
@import url("https://fonts.googleapis.com/css2?family=Russo+One&display=swap");

svg {
    font-family: "Russo One", sans-serif;
    width: 100%;
    height: 100%;
}

svg text {
    animation: stroke 5s infinite alternate;
    stroke-width: 2;
    stroke: #FF0000;
    font-size: 100px;
}

@keyframes stroke {
    0% {
        fill: rgba(9, 0, 204, 0);
        stroke: rgba(255, 0, 0, 1);
        stroke-dashoffset: 25%;
        stroke-dasharray: 0 50%;
        stroke-width: 2;
    }

    70% {
        fill: rgba(9, 0, 204, 0);
        stroke: rgba(255, 0, 0, 1);
    }

    80% {
        fill: rgba(9, 0, 204, 0);
        stroke: rgba(255, 0, 0, 1);
        stroke-width: 3;
    }

    100% {
        fill: rgba(9, 0, 204, 1);
        stroke: rgba(255, 0, 0, 0);
        stroke-dashoffset: -25%;
        stroke-dasharray: 50% 0;
        stroke-width: 0;
    }
}

.wrapper {
    background-color: #FFFFFF
}

;
</style>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <div class="wrapper">
            <svg>
                <text x="50%" y="50%" dy=".35em" text-anchor="middle">
                    GCMS
                </text>
            </svg>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="username" value="username" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                    autocomplete="current-password" />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">

            </div>

            <div class="flex items-center justify-end mt-4">


                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
