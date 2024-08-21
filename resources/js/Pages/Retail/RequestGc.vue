<template>
    <a-card title="Store GC Request Form">
        <div class="flex justify-end mb-2">
            <a-button @click="submitForm" type="primary">
                <CheckOutlined /> Submit Form
            </a-button>
        </div>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <a-card>
                    <a-form-item label="">
                        <a-table :dataSource="denoms" :columns="denomColumns" :pagination="false">
                            <template #bodyCell="{ column, record, index }">
                                <!-- {{record}} -->
                                <template v-if="column.key === 'qty'">
                                    <a-input v-model:value="form.quantities[record.denom_id]" @input="updateTotal"></a-input>
                                </template>
                            </template>
                        </a-table>
                        <div class="flex justify-end">
                            Total: {{ countDenom }}
                        </div>
                    </a-form-item>

                </a-card>
            </a-col>
            <a-col :span="8">
                <a-card>
                    <a-form-item label="GC Request No">
                        <a-input></a-input>
                    </a-form-item>
                    <a-form-item label="Retail Store">
                        <a-input></a-input>
                    </a-form-item>
                    <a-form-item label="Date Requested">
                        <a-input></a-input>
                    </a-form-item>
                    <a-form-item label="Upload Doc">
                        <a-upload-dragger v-model:fileList="fileList" name="file" :multiple="false"
                            @change="handleChange"
                            @drop="handleDrop">
                            <p class="ant-upload-drag-icon">
                                <inbox-outlined></inbox-outlined>
                            </p>
                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                        </a-upload-dragger>
                    </a-form-item>
                    <a-form-item label="Remarks">
                        <a-input></a-input>
                    </a-form-item>
                    <a-form-item label="Prepared by">
                        <a-input></a-input>
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="8">
                <a-card title="Allocated GC">
                 {{ $page.props.auth.user.store_assigned}}
                </a-card>
            </a-col>
        </a-row>

    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        denoms: Array,
        denomColumns: Array
    },
    data() {
        return {
            form: {
                quantities: [],
            }
        }
    },
    computed: {
        countDenom() {
            return this.denoms.reduce((sum, record, index) => {
                const qty = parseInt(this.form.quantities[index], 10);
                const denomination = parseInt(record.denomination, 10);
                const total = isNaN(qty) || isNaN(denomination) ? 0 : qty * denomination;
                return sum + total;
            }, 0);
        }
    },
    methods: {
        updateTotal() {
            this.$forceUpdate();
        },
        submitForm() {
            this.$inertia.post(route('retail.gc.request.submit'), {
                data: this.form
            });
        }
    }
}
</script>
