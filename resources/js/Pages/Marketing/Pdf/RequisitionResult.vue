<template>
    <a-result status="success" title="Successfully PDF Generated!"
        sub-title="">
        <template #extra>
            <a-button key="console" type="primary" @click="download">Download pdf</a-button>
        </template>
    </a-result>
</template>
<script>
export default {
    props: {
        filePath: String
    },
    methods: {
        download() {
            window.location.href = this.filePath;

            this.$inertia.get(route('marketing.dashboard'));
        }
    }
}
</script>