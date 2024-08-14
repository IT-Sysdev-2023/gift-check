<template>
    <a-table :data-source="scannedGc" :columns="columns" size="small">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key == 'remove'">
                <a-button size="small" type="primary" danger @click="remove(record.tval_barcode)">
                    <template #icon>
                        <ClearOutlined />
                    </template>
                    Remove
                </a-button>
            </template>
        </template>
    </a-table>
</template>
<script>

import { notification } from 'ant-design-vue';
export default {
    props: {
        scannedGc: Object
    },
    data() {
        return {
            columns: [
                {
                    title: 'Barcode Number',
                    dataIndex: 'tval_barcode',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                },
                {
                    title: 'Action',
                    key: 'remove',
                    align: 'center'
                },
            ],
        }
    },
    methods: {
        remove(barcode) {
            this.$inertia.post(route('iad.remove.scanned.gc'), {
                barcode: barcode
            }, {
                onSuccess: (reponse) => {
                    notification[reponse.props.flash.status]({
                        message: reponse.props.flash.title,
                        description:
                        reponse.props.flash.msg,
                    });
                }
            })
        }
    }
}
</script>
