<template>

    <Head title="Released Promo GC" />
    <a-card style="width: 24rem;">
        <a-card class="mb-2" title="Released Promo GC"></a-card>
        <a-form-item label="Date Released:" name="dateReleased">
            <a-date-picker v-model:value="form.dateReleased" disabled />
        </a-form-item>
        <a-form-item label="Received by:" name="recievedBy">
            <a-input v-model:value="form.receiveBy" />
        </a-form-item>
        <a-form-item label="Address:" name="address">
            <a-input v-model:value="form.address" />
        </a-form-item>
        <a-form-item label="Barcode:" name="barcode">
            <a-input v-model:value="form.barcode" showCount />
        </a-form-item>
        <a-form-item label="Released By:" name="promoNo">
            <a-input v-model:value="form.releasedByname" disabled />
        </a-form-item>

        <a-form-item>
            <a-button @click="submit" type="primary" html-type="submit">Submit</a-button>
        </a-form-item>
    </a-card>
</template>

<script>
import { PlusOutlined, BarcodeOutlined } from "@ant-design/icons-vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { notification } from 'ant-design-vue';
import dayjs from "dayjs";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    components: {
        PlusOutlined,
        BarcodeOutlined,
    },

    data() {
        return {
            form: {
                dateReleased: dayjs(),
                receiveBy: null,
                address: null,
                barcode: null,
                releasedByname: this.$page.props.auth.user.full_name,
                releasedById: this.$page.props.auth.user.user_id,
            }
        }
    },
    methods: {
        submit() {
            axios.post(route('marketing.releaseGc.gcpromoreleased'), {
                data: this.form
            }).then(response => {
                console.log(response.data.response.type);

                notification[response.data.response.type]({
                    message: response.data.response.msg,
                    description: response.data.response.description,
                });
            })
        }
    }
};
</script>
