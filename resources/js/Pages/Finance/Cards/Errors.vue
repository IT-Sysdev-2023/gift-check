<template>
    <div class="ml-1 text-red-500" style="border: 1px solid #FF9B9B; border-radius: 5px; font-size: 13px;">
        * {{ errors }}
    </div>
</template>
<script>
export default {
    props: {
        errors: String
    }
}
</script>
