<template>
    <a-result status="success" title="Successfully Generated Pdf"
        sub-title="You can download the pdf in the download here button thank you.">
        <template #extra>
            <a-button key="console" @click="() => $inertia.get(route('finance.approved.released.reports'))">
                <template #icon>
                    <ArrowLeftOutlined />
                </template> Return Back
            </a-button>
        </template>
        <a-card class="mb-5">
            <a-button @click="downloadFunction">
                <template #icon>
                    <CloudDownloadOutlined />
                </template>
                Click here to Download the Result below...
            </a-button>
        </a-card>
        <a-card>
            
            <iframe :src="pdfUrl" width="100%" height="1500px"></iframe>
        </a-card>
    </a-result>
</template>
<script>
export default {
    props: {
        filePath: Object,
        stream: Object,
    },
    data() {
        return {
            pdfUrl: `data:application/pdf;base64,${this.stream}`,
        }
    },
    methods: {
        downloadFunction() {
            window.location.href = this.filePath;
        }
    },
}
</script>
