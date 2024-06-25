<template>
    <div>
        <a-typography-text code>
            {{ progressBarHeader.message }}
        </a-typography-text>
        <a-progress stroke-color="#52c41a" size="small" :percent="progressBarHeader.percentage" :steps="293" />
    </div>
</template>
<script>
export default {
    props: {
        progressBarHeader: Object
    }
}
</script>
