<template>
    <a-card title="Store GC Request">
        <a-row>
            <a-col :span="8">
                <a-button @click="() => $inertia.get(route('retail.gcrequest.pending.list'))">
                    <RollbackOutlined /> Back
                </a-button>
            </a-col>
            <a-col :span="8">
                <div v-if="isToggled == true" class="flex justify-center">
                    <h4>Edit Mode</h4>
                </div>
            </a-col>
            <a-col :span="8">
                <div class="flex justify-end mb-4">
                    <a-button @click="toggleState" type="primary" class="mx-5">Edit</a-button>
                    <div v-if="isToggled == true">
                        <a-button class="bg-green-500" @click="update">Update Request</a-button>
                    </div>
                    <div v-else>
                        <a-button @click="cancelModalopen" danger>Cancel Request</a-button>
                    </div>
                </div>
            </a-col>
        </a-row>
        <div v-if="isToggled == false">
            <a-row :gutter="[16, 16]">
                <a-col :span="18">
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-card>
                                <a-form-item label="GC Request No.">
                                    <a-input v-model:value="form.gcRequestNumber" readonly></a-input>
                                </a-form-item>
                                <a-form-item label="Retail Store">
                                    <a-input v-model:value="form.storeName" readonly></a-input>
                                </a-form-item>
                                <a-form-item label="Date Requested">
                                    <a-input v-model:value="details[0].dateRequest" readonly></a-input>
                                </a-form-item>
                            </a-card>
                        </a-col>
                        <a-col :span="12">
                            <a-card>
                                <a-form-item label="Date Needed">
                                    <a-input v-model:value="details[0].dateNeed" readonly></a-input>
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-input v-model:value="form.sgc_remarks" readonly></a-input>
                                </a-form-item>
                                <a-form-item label="Prepared by">
                                    <a-input v-model:value="form.requestedBy" readonly></a-input>
                                </a-form-item>
                            </a-card>
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :span="6">
                    <a-table :dataSource="barcode" :columns="columns" :pagination="false" bordered />
                </a-col>
            </a-row>
        </div>
        <div v-else>
            <a-row :gutter="[16, 16]">
                <a-col :span="18">
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-card>
                                <a-form-item label="GC Request No.">
                                    <a-input v-model:value="form.gcRequestNumber" readonly></a-input>
                                </a-form-item>
                                <a-form-item label="Retail Store">
                                    <a-input v-model:value="form.storeName" readonly></a-input>
                                </a-form-item>
                                <a-form-item label="Date Requested">
                                    <a-input v-model:value="details[0].dateRequest" readonly></a-input>
                                </a-form-item>
                            </a-card>
                        </a-col>
                        <a-col :span="12">
                            <a-card>
                                <a-form-item label="Date Needed">
                                    <a-date-picker :disabled-date="disabledDate" v-model:value="form.dateNeed" />
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-input v-model:value="form.sgc_remarks"></a-input>
                                </a-form-item>
                                <a-form-item label="Prepared by">
                                    <a-input v-model:value="form.requestedBy" readonly></a-input>
                                </a-form-item>
                            </a-card>
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :span="6">
                    <!-- {{barcode}} -->
                    <a-form-item label="">
                        <a-table bordered :dataSource="denoms" :columns="denomColumns" :pagination="false">

                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'qty'">
                                    <a-input v-model:value="record.quantity"></a-input>
                                </template>
                            </template>
                        </a-table>
                    </a-form-item>
                </a-col>
            </a-row>
        </div>
    </a-card>

    <a-modal v-model:open="cancelModal" title="Cancel Request" @ok="handleOk">
        <a-form-item label="Type 'confirm' to cancel request">
            <a-input v-model:value="confirmCancell" placeholder="confirm" @keyup.enter="cancellRequest"></a-input>
            <div>
                <small v-if="error == true" class="text-red-500">*incorect input</small>
            </div>
        </a-form-item>
        <template #footer>
            <a-button key="back" @click="handleCancel">No</a-button>
            <a-button key="submit" type="primary" :loading="loading" @click="cancellRequest">Yes</a-button>
        </template>
    </a-modal>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        details: Object,
        barcode: Object,
        columns: Object,
        denoms: Object,
        denomColumns: Object
    },
    data() {
        return {
            isToggled: false,
            cancelModal: false,
            confirmCancell: '',
            error: false,

            form: {
                gcRequestNumber: this.details[0].sgc_num,
                storeName: this.details[0].store_name,
                dateRequested: '',
                dateNeed: '',
                requestedBy: this.details[0].requestedBy,
                sgc_remarks: this.details[0].sgc_remarks,
                denom: [],
            },
        }
    },
    methods: {
        disabledDate(current) {
            return current && current < new Date().setHours(0, 0, 0, 0);
        },
        toggleState() {
            this.isToggled = !this.isToggled;
        },
        cancelModalopen() {
            this.cancelModal = true
        },
        cancellRequest() {
            if (this.confirmCancell != 'confirm') {
                this.error = true
            } else {
                this.$inertia.get(route('retail.gcrequest.cancel'), {
                    id: this.details[0].sgc_id
                }, {
                    onSuccess: (response) => {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description,
                        });
                        this.$inertia.get(route('retail.gcrequest.pending.list'))
                    }
                });
            }
        },
        update() {
            const quantities = this.denoms.map(record => ({
                quantity: record.quantity,
                id: record.denom_id,
            }));

            this.$inertia.put(route('retail.gcrequest.update'), {
                id: this.details[0].sgc_id,
                denom: quantities
            })
            this.isToggled = false
        }
    }
}
</script>
