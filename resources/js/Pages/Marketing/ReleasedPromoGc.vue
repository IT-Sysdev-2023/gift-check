<script>
import { PlusOutlined, BarcodeOutlined } from "@ant-design/icons-vue";
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    components: {
        PlusOutlined,
        BarcodeOutlined
    }
};
</script>

<template>
    <Head title="Released Promo GC" />
    <a-card style="width: 24rem;">
        <a-card class="mb-2" title="Released Promo GC"></a-card>
        <a-form>
            <a-form-item label="Date Released:" name="dateReleased">
                <a-date-picker v-model:value="dateReleased" disabled/>
            </a-form-item>
            <a-form-item label="Received by:" name="recievedBy">
                <a-input v-model:value="promoNo" />
            </a-form-item>
            <a-form-item label="Address:" name="address">
                <a-input v-model:value="promoNo" />
            </a-form-item>
            <a-form-item label="Released By:" name="promoNo">
                <a-input v-model:value="$page.props.auth.user.full_name" disabled />
            </a-form-item>

            <a-form-item>
                <a-button type="primary" html-type="submit">Submit</a-button>
            </a-form-item>
        </a-form>
    </a-card>
</template>
