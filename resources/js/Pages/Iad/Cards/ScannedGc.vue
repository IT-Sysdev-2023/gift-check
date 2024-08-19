<template>
    <a-table :data-source="scannedGc" :columns="columns" size="small" bordered>
        <template #bodyCell="{ column, record }">
            <template v-if="column.key == 'remove'">
                <a-popconfirm :title="'Remove this? '+ record.tval_barcode" ok-text="Yes" cancel-text="No"
                    @confirm="remove(record.tval_barcode)" @cancel="cancel">
                    <a-button size="small" type="primary" danger>
                        <template #icon>
                            <ClearOutlined />
                        </template>
                        Remove
                    </a-button>
                </a-popconfirm>

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
                onSuccess: (response) => {
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description:
                        response.props.flash.msg,
                    });
                }
            })
        }
    }
}
</script>
