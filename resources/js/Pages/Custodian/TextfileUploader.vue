<template>
    <AuthenticatedLayout>
        <a-card>
            <p class="text-xl mb-4 ml-1">
                <UploadOutlined /> Textfile Uploader
            </p>
            <a-upload-dragger  :before-upload="() => false" @change="() => response = []" :max-count="1" v-model:fileList="form.fileList" :height="200" name="file" :action="false" accept=".txt"
                :multiple="false">
                <p class="ant-upload-drag-icon">
                    <inbox-outlined></inbox-outlined>
                </p>
                <p class="ant-upload-text">Click or drag file to this area to upload</p>
                <p class="ant-upload-hint">
                    "Every upload is a step forward, a moment captured and shared, connecting us to a world beyond our
                    screens."
                </p>
            </a-upload-dragger>
            <div class="flex justify-end">
                <a-button class="mt-5" @click="upload" type="primary" :disabled="form.fileList.length === 0">
                    <template #icon>
                        <CloudUploadOutlined />
                    </template>
                    Upload Textfile Submit
                </a-button>
            </div>
            <a-timeline size="small">
                <p class="mb-4" v-if="Object.keys(response).length">
                    <QuestionCircleOutlined />   Missing Formats
                </p>
                <a-timeline-item  v-for="res in response"  color="red">

                    <p style="color: red;">{{ res  }}</p>
                </a-timeline-item>
            </a-timeline>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';
const form = useForm({
    fileList: []
});

const response = ref({});

const upload = () => {
    router.post(route('custodian.upload'), {
        file: form.fileList
    }, {
        onSuccess: (res) => {

            notification[res.props.flash.status]({
                message: res.props.flash.title,
                description: res.props.flash.msg,
            });

            response.value = res.props.flash.data;
        }
    });
}
</script>
