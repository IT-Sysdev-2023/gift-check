<template>
    <AuthenticatedLayout>
        <a-card>
            <template #title>
                <div class="flex justify-end">
                    <a-button @click="uploadDesign">
                        Upload Design
                    </a-button>
                </div>
            </template>
            <a-row :gutter="[16, 16]">
                <a-col :span="8">
                    <a-image alt="design" />
                </a-col>
            </a-row>
        </a-card>
        <a-modal v-model:open="modal" :width="500" title="Upload Design" :footer="null">
            <a-card>
                <a-form :model="formState" name="basic" :label-col="{ span: 8 }" :wrapper-col="{ span: 16 }"
                    autocomplete="off" @finish="onFinish" @finishFailed="onFinishFailed">
                    <span class="ml-1">Design Name</span>
                    <a-input class="mb-2" v-model:value="formState.name" />

                    <p class="text-center">Upload</p>
                    <div class="flex justify-center">
                        <ant-upload-image v-model:value="formState.image" />
                    </div>

                    <a-button type="primary" block class="mt-5" html-type="submit">Submit</a-button>

                </a-form>
            </a-card>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { route } from 'ziggy-js';

interface FormState {
    name: string;
    image: string;
}

const formState = useForm<FormState>({
    name: '',
    image: '',
});

const modal = ref<boolean>(false);

const uploadDesign = () => {
    modal.value = true;
}
const onFinish = () => {
    formState.transform((data) => ({
        ...data,
    })).post(route('admin.masterfile.upload.image'))

};

const onFinishFailed = (errorInfo: any) => {
    console.log('Failed:', errorInfo);
};
</script>
