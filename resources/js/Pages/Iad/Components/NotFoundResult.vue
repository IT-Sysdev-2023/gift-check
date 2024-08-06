<template>
    <a-result status="404" title="404">
        <template #subTitle>
            Sorry the Requisition {{ requis }} does not Exists..
        </template>
        <template #extra>
            <a-button type="primary" @click="() => $inertia.get(route('iad.receiving'))">Back to Fad Textfiles
                Details</a-button>
        </template>
    </a-result>
</template>
<script>
export default {
    props: {
        requis: Number,
    }
}
</script>
