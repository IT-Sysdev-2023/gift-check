<template>
    <a-modal style="width: 30%;" title="Managers Key" :footer="null" :afterClose="close">
        <a-form class="mt-5" :model="formState">
            <a-form-item autocomplete="off">
                <a-input v-model:value="formState.username" autocomplete="none" placeholder="Username">
                    <template #prefix>
                        <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                    </template>
                </a-input>
            </a-form-item>
            <a-form-item>
                <a-input v-model:value="formState.password" type="password" placeholder="Password">
                    <template #prefix>
                        <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                    </template>
                </a-input>
            </a-form-item>
            <a-form-item class="text-right">
                <a-button @click="submit" type="primary"
                    :disabled="formState.username === '' || formState.password === ''">
                    Submit Credentials
                </a-button>
            </a-form-item>
        </a-form>
    </a-modal>
</template>
<script lang="ts" setup>
import { UserOutlined, LockOutlined } from '@ant-design/icons-vue';
import { useForm } from 'laravel-precognition-vue';

interface FormState extends Record<string, unknown> {
    username: string;
    password: string;
}

const props = defineProps<{
    routeUrl: string
}>();

const emit = defineEmits<{
    (e: "closemodal", value: number | string): void;
}>();

const formState = useForm<FormState>('post', route(props.routeUrl) ?? '', {
    username: '',
    password: '',
} as FormState);


const close = () => {
    formState.reset();
}
const submit = () => {
    formState.submit().then((res: any) => {
        if (res.data.status == 'success') {
            emit('closemodal', res.data);
        }
    });
}
</script>
