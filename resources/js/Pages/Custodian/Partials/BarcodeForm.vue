<template>
    <a-card>
        <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
            <a-descriptions-item label="Date Scanned" class="text-right">{{ date }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item label="Scanned By " class="text-right">{{ $page.props.auth.user.full_name
                }}</a-descriptions-item>
        </a-descriptions>

        <div class="text-center mt-4">Scan Barcode</div>
        <a-input prefix="#" show-count allow-clear @keypress="handleKeyPress" @keyup.enter="submit"
            v-model:value="form.barcode" size="large" />

        <a-button block class="mt-2" type="primary" @click="submit"
            :disabled="form.barcode === null || form.barcode === ''">Submit</a-button>

        <a-alert v-if="status.status" class="mt-2" :message="status.desc" :type="status.status" show-icon />

    </a-card>
</template>

<script>

import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';

export default {
    props: {
        date: String
    },
    data() {
        return {
            form: {
                barcode: null,
            },
            status: {},
        }
    },
    methods: {
        submit() {
            axios.post(route('custodian.scan.barcode'), { ...pickBy(this.form) })
                .then(response => {
                    notification[response.data.status]({
                        message: response.data.msg,
                        description: response.data.desc,
                    });

                    this.status = response.data
                });
        },
        handleKeyPress(event) {
            const charCode = event.which ? event.which : event.keyCode;
            if (charCode < 48 || charCode > 57) {
                event.preventDefault();
            }
        }
    }
}
</script>
