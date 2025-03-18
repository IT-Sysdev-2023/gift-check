<template>
    <a-modal style="width: 30%;" title="Managers Key" :footer="null" :afterClose="close">
        <a-alert v-if="response" :message="response.title + ' - ' + response.msg" :type="response.status" show-icon />
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
                <a-button @click="submit" type="primary"  @keyup.enter="submit"
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
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

interface FormState extends Record<string, unknown> {
    username: string;
    password: string;
}

const props = defineProps<{
    routeUrl: string,
    keyValue: number,
}>();

const emit = defineEmits<{
    (e: "closemodal", value: number | string, keyvalue: number): void;
}>();

const formState = useForm<FormState>('post', route(props.routeUrl) ?? '', {
    username: '',
    password: '',
} as FormState);

interface Response {
    msg: string,
    status: string,
    title: string,
}
const response = ref<Response | null>(null);

const close = () => {
    response.value = null;
    formState.reset();
}
const submit = () => {
    formState.submit().then((res: any) => {
        response.value = res.data;
        if (res.data.status == 'success') {
            response.value = null;
            notification['success']({
                message: res.data.title,
                description: res.data.msg,
            });
            emit('closemodal', res.data, props.keyValue);
        } else {
            notification[res.data.status]({
                message: res.data.title,
                description: res.data.msg,
            });
        }
    });
}
</script>
