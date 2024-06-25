<template>
    <div>
        <a-typography-text code>
            {{ progressBarInner.message }}
        </a-typography-text>
        <a-typography-text keyboard>
            {{ progressBarInner.currentRow }} to {{ progressBarInner.totalRows }}
        </a-typography-text>
        <a-progress stroke-color="#52c41a" size="small" :percent="progressBarInner.percentage" :steps="293" />
    </div>
</template>
<script>
export default {
    props: {
        progressBarInner: Object
    }
}
</script>
