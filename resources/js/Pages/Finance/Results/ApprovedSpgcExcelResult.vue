<template>
    <a-result status="success" title="Successfully Generated Excel"
        sub-title="You can download the Excel in the download here button thank you.">
        <template #extra>
            <a-button key="console" @click="() => $inertia.get(route('finance.approved.released.reports'))">
                <template #icon>
                    <ArrowLeftOutlined />
                </template> Return Back
            </a-button>
        </template>
        <a-card>
            <a-button @click="downloadFunction">
                <template #icon>
                    <CloudDownloadOutlined />
                </template>
                Click here to Download...
            </a-button>
        </a-card>
    </a-result>
</template>
<script>
export default {
    props: {
        filePath: Object
    },
    methods: {
        downloadFunction() {
            window.location.href = this.filePath;
        }
    }
}
</script>
