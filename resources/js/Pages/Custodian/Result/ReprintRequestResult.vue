<template>
    <a-result status="success" title="Successfully Generated Pdf"
        sub-title="You can download the pdf in the download here button thank you">
        <template #extra>
            <a-button key="console" @click="() => $inertia.get(route('custodian.dashboard'))">
                <template #icon>
                    <ArrowLeftOutlined />
                </template> Return Back
            </a-button>
        </template>
        <a-card class="mb-5">
            <a-button @click="downloadFunction" type="primary">
                <template #icon>
                    <CloudDownloadOutlined />
                </template>
                Click here to Download the Result below
            </a-button>
        </a-card>
        <a-card>
            <iframe :src="pdfUrl" width="100%" height="1500px"></iframe>
        </a-card>
    </a-result>
</template>
<script setup>
import { ref } from 'vue';

const props = defineProps({
    filePath: Object,
    stream: Object,
    id: Number
});
const pdfUrl = ref(`data:application/pdf;base64,${props.stream}`);

const downloadFunction = () => {
    window.location.href = props.filePath;
}

</script>
