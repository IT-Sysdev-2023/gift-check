<template>
    <div>
        <a-typography-text code>
            {{ progressBar.message }}
        </a-typography-text>
        <a-typography-text keyboard>
            {{ progressBar.currentRow }} to {{ progressBar.totalRows }}
        </a-typography-text>
        <a-progress stroke-color="#52c41a" size="small" :percent="progressBar.percentage" :steps="293" />
    </div>
</template>
<script>
export default {
    props: {
        progressBar: Object
    }
}
</script>
