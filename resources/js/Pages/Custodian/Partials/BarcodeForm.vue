<template>
    <a-card>
        <a-input-group compact>
            <span>Scan Barcode:</span>
            <a-input show-count v-model:value="form.barcode" style="width: calc(100% - 31px)" @keyup.enter="submit" />
            <a-tooltip title="Scan Barcode here..">
                <a-button :loading="form.processing">
                    <template #icon>
                        <BarcodeOutlined />
                    </template>
                </a-button>
            </a-tooltip>
        </a-input-group>
        <a-button block class="mt-2" type="primary" @click="submit">Submit</a-button>

        <a-alert v-if="status.status" class="mt-2" :message="status.desc" :type="status.status" show-icon />

    </a-card>


    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
        <a-descriptions-item label="Date Scanned" class="text-right">{{ date }}</a-descriptions-item>
    </a-descriptions>
    <a-descriptions size="small" layout="horizontal" bordered>
        <a-descriptions-item label="Scanned By " class="text-right">{{ $page.props.auth.user.full_name
            }}</a-descriptions-item>
    </a-descriptions>
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
        }
    }
}
</script>
