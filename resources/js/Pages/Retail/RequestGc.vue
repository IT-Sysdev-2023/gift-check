<template>
    <a-card title="Store GC Request Form">
        <div class="flex justify-end mb-2">
            <a-button title="Please fill required fields"
                v-if="(form.dateNeed == '' || form.remarks == '' || form.quantities.filter((data) => data).length == 0)" @click="submitForm"
                type="primary" disabled>
                <CheckOutlined /> Submit Form
            </a-button>
            <a-button v-else @click="submitForm" type="primary">
                <CheckOutlined /> Submit Form
            </a-button>
        </div>
       
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <a-card>
                    <a-form-item label="">
                        <a-table :dataSource="denoms" :columns="denomColumns" :pagination="false">
                            <template #bodyCell="{ column, record, index }">
                                <template v-if="column.key === 'qty'">
                                    <a-input type="number" v-model:value=" form.quantities[record.denom_id]"
                                        @change="calculateTotal(record)"></a-input>
                                </template>
                            </template>
                        </a-table>
                        <div class="flex justify-end">
                            Total: {{ total }}
                        </div>
                    </a-form-item>
                </a-card>
            </a-col>

            <a-col :span="8">
                <a-card>
                    <a-form-item label="GC Request No">
                        <a-input v-model:value="form.requestNumber" readonly></a-input>
                    </a-form-item>
                    <a-form-item label="Retail Store">
                        <a-input v-model:value="form.storeName" readonly></a-input>
                    </a-form-item>
                    <a-form-item label="Date Requested">
                        <a-input v-model:value="form.dateReq" readonly></a-input>
                    </a-form-item>
                    <a-form-item label="Date Needed">
                        <a-date-picker style="width: 100%;" v-model:value="form.dateNeed" />
                        <small style="color: red;" v-if="form.dateNeed ==''">*this field is required</small>
                    </a-form-item>
                    <a-form-item label="Upload Doc">
                        <a-upload-dragger name="file" :before-upload="() => false" :max-count="1"
                            @change="handleImageChange" @drop="handleDrop">
                            <p class="ant-upload-drag-icon">
                                <inbox-outlined></inbox-outlined>
                            </p>
                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                        </a-upload-dragger>
                    </a-form-item>
                    <a-form-item label="Remarks">
                        <a-textarea v-model:value="form.remarks"></a-textarea>
                        <small style="color: red;" v-if="form.remarks ==''">*this field is required</small>
                    </a-form-item>
                    <a-form-item label="Prepared by">
                        <a-input v-model:value="form.approvedBy" readonly></a-input>
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="8">
                <a-table :dataSource="allocated" :columns="allocatedGcColumns" :pagination="false" bordered>
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'qty'">
                            <a-input style="width:40px;" :value="record.count" readonly></a-input>
                        </template>
                    </template>
                </a-table>
            </a-col>
        </a-row>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { notification } from 'ant-design-vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        denoms: Array,
        denomColumns: Array,
        requestno: String,
        storeName: String,
        allocatedGcColumns: String,
        requestNumber: Number,
        allocated: Object,
    },
    data() {
        return {
            file: null,
            form: {
                sgc_id: this.requestno.sgc_id,
                quantities: [],
                requestNumber: this.requestNumber,
                storeName: this.storeName[0]['store_name'],
                dateReq: dayjs().format('YYYY-MM-DD'),
                dateNeed: '',
                remarks: '',
                approvedBy: this.$page.props.auth.user.full_name,
                approvedById: this.$page.props.auth.user.user_id,
            },
            total: 0,
        }
    },
   
    methods: {
        calculateTotal(data) {

            this.total = this.denoms.reduce((sum, denom) => {
                const qty = this.form.quantities[denom.denom_id] || 0;
                return sum + (denom.denomination * qty);
            }, 0);
        },
        submitForm() {
            this.$inertia.post(route('retail.gc.request.submit'), {
                data: this.form,
                file: this.file,
            }, {
                onSuccess: (response) => {
                    notification[response.props.flash.type]({
                        message: response.props.flash.msg,
                        description: response.props.flash.description,
                    });
                    this.$inertia.get(route('retail.dashboard'))
                }
            });
        },
        handleImageChange(document) {
            this.file = document.file;
        },
    }
}
</script>
