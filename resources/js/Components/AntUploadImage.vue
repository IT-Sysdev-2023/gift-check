<script setup lang="ts">
import type { UploadProps, UploadChangeParam } from "ant-design-vue";
import { ref } from "vue";

function getBase64(file: File) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
}

const fileList = ref([]);

//IMAGE UPLOAD
const previewVisible = ref(false);
const previewImage = ref("");
const previewTitle = ref("");

const handleCancel = () => {
    previewVisible.value = false;
    previewTitle.value = "";
};
const handlePreview = async (file: UploadProps["fileList"][number]) => {
    if (!file.url && !file.preview) {
        file.preview = (await getBase64(file.originFileObj)) as string;
    }
    previewImage.value = file.url || file.preview;
    previewVisible.value = true;
    previewTitle.value =
        file.name || file.url.substring(file.url.lastIndexOf("/") + 1);
};
const emit = defineEmits(['handleChange']);
const handleUploadChange = (info: UploadChangeParam) => {
    emit('handleChange', info);
};
</script>

<template>
    <div class="clearfix">
        <a-upload
            v-model:file-list="fileList"
            :before-upload="() => false"
            list-type="picture-card"
            @preview="handlePreview"
            @change="handleUploadChange"
            :max-count="1"
        >
            <div v-if="fileList.length == 0">
                <plus-outlined />
                <div style="margin-top: 8px">Upload</div>
            </div>
            <div v-else>
                <SwapOutlined />
                <div style="margin-top: 8px">Replace</div>
            </div>
        </a-upload>
        <a-modal
            :open="previewVisible"
            :title="previewTitle"
            :footer="null"
            @cancel="handleCancel"
        >
            <img alt="example" style="width: 100%" :src="previewImage" />
        </a-modal>
    </div>
</template>
<style scoped>
/* you can make up upload button and sample style by using stylesheets */
.ant-upload-select-picture-card i {
    font-size: 32px;
    color: #999;
}

.ant-upload-select-picture-card .ant-upload-text {
    margin-top: 8px;
    color: #666;
}
</style>