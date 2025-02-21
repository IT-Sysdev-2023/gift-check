<template>
    <AuthenticatedLayout>
        <a-breadcrumb>
            <a-breadcrumb-item>
                <Link href="/">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link href="dti-pending-special-gc">DTI Pending GC List (GC Holder Entry)</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>DTI GC Holder Entry</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card title="DTI GC Holder Entry">
            <a-row :gutter="[16, 16]">
                <a-col :span="8">
                    <a-form-item label="GC Request #">
                        <a-input readonly :value="data.dti_num" />
                    </a-form-item>
                    <a-form-item label="Date Requested">
                        <a-input readonly :value="data.dateRequested" />
                    </a-form-item>
                    <a-form-item label="Date Validity">
                        <a-input readonly :value="data.validity" />
                    </a-form-item>
                    <a-form-item label="Document(s) Uploaded">
                        <a-image class="mb-2 h-[100px]" :key="index" v-for="(item, index) in props.data.dti_documents"
                            :src="`/storage/${item.dti_fullpath}`" />
                    </a-form-item>
                    <a-form-item label="Upload Document" has-feedback :validate-status="form.errors.file ? 'error' : ''"
                        :help="form.errors.file">
                        <ant-upload-image @handleChange="handleImage" />
                    </a-form-item>
                </a-col>
                <a-col :span="8">
                    <a-form-item label="Company Name">
                        <a-input readonly :value="data.spcus_companyname" />
                    </a-form-item>
                    <a-form-item label="Account Name">
                        <a-input readonly :value="data.spcus_acctname" />
                    </a-form-item>
                    <a-form-item label="AR #">
                        <a-input readonly :value="data.dti_payment_arno" />
                    </a-form-item>
                    <a-form-item label="Payment Type">
                        <a-input readonly :value="data.dti_paymenttype" />
                    </a-form-item>
                    <a-form-item label="Amount Paid">
                        <a-input readonly :value="data.dti_payment" />
                    </a-form-item>
                    <a-form-item label="Amount in words">
                        <a-input readonly :value="`${data.amountInWords} Pesos`" />
                    </a-form-item>
                </a-col>
                <a-col :span="8">
                    <a-form-item label="Remarks">
                        <a-textarea readonly :value="data.dti_remarks" />
                    </a-form-item>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="8">
                            <a-form-item label="Demomination">
                                <a-input readonly :value="data.dti_denoms" />
                            </a-form-item>
                        </a-col>
                        <a-col :span="6">
                            <a-form-item label="Qty">
                                <a-input readonly :value="data.dti_qty" />
                            </a-form-item>
                        </a-col>
                        <a-col :span="4">
                            <div class=" h-16 flex items-end">
                                <a-button @click="openModal" type="primary">
                                    <UserAddOutlined />
                                </a-button>
                            </div>
                        </a-col>
                        <a-col :span="6">
                            <a-form-item label="# Holder" has-feedback
                                :validate-status="form.errors.holders ? 'error' : ''" :help="form.errors.holders">
                                <a-input readonly :value="gcHolder.length ? gcHolder.length : 0" />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-form-item>
                        <div class="h-14 flex items-center">
                            <p class=" text-3xl">Total: {{ `₱ ${data.total}` }}</p>
                        </div>
                    </a-form-item>
                    <a-form-item label="Entry By">
                        <a-input readonly
                            :value="$page.props.auth.user.firstname + ' ' + $page.props.auth.user.lastname" />
                    </a-form-item>
                    <div>
                        <div class="flex justify-end">
                            <a-button @click="handleSubmit" type="primary">Submit </a-button>
                        </div>
                    </div>
                </a-col>
            </a-row>
        </a-card>
        <a-modal :footer="false" v-model:open="open" width="300" title="Assign Customer Employee" @ok="handleOk">
            <a-row :gutter="[16, 16]">
                <a-col :span="8">
                    <a-form-item label="Last Name">
                        <a-input placeholder="This field is required" v-model:value="holderData.lastname" />
                    </a-form-item>
                    <a-form-item label="First Name">
                        <a-input placeholder="This field is required" v-model:value="holderData.firstname" />
                    </a-form-item>
                    <a-form-item label="Middle Name">
                        <a-input placeholder="This field is required" v-model:value="holderData.mname" />
                    </a-form-item>
                    <a-form-item label="Name Ext.">
                        <a-input v-model:value="holderData.ext" />
                    </a-form-item>
                    <a-form-item label="Address">
                        <a-input placeholder="This field is required" v-model:value="holderData.address" />
                    </a-form-item>
                    <a-form-item label="Voucher">
                        <a-input placeholder="This field is required" v-model:value="holderData.voucher" />
                    </a-form-item>
                    <a-form-item label="Business Unit">
                        <a-input placeholder="This field is required" v-model:value="holderData.bu" />
                    </a-form-item>
                    <div class="flex justify-end gap-3">
                        <a-button @click="handleAssign" type="primary">Assign</a-button>
                        <a-button @click="clear" type="primary" danger>Clear</a-button>
                    </div>
                </a-col>
                <a-col :span="16">
                    <p>Denomination: {{ data.dti_denoms }} </p>
                    <div class="h-[500px] overflow-y-auto">
                        <a-card>
                            <a-table :pagination="false" size="small" :dataSource="gcHolder" :columns="columns" />
                        </a-card>
                    </div>
                </a-col>
            </a-row>
        </a-modal>
    </AuthenticatedLayout>
</template>


<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { notification } from 'ant-design-vue';
const open = ref(false);
const gcHolder = ref([]);

const holderData = ref({
    lastname: '',
    firstname: '',
    mname: '',
    ext: '',
    address: '',
    voucher: '',
    bu: '',
})
const props = defineProps({
    data: Object
})

const form = useForm({
    file: null,
    holders: computed(() => gcHolder.value),
});



const handleSubmit = () => {
    form.post(route('custodian.dti_special_gcsubmit_dti_special_gc'), {
        onSuccess: () => {
            router.push('dti-pending-special-gc')
        }
    })
}



const handleAssign = () => {
    if (!holderData.value.lastname || !holderData.value.firstname || !holderData.value.mname || !holderData.value.address || !holderData.value.voucher || !holderData.value.bu) {
        notification['error']({
            message: 'Notification Title',
            description:
                'Please fill in all required fields.'
        });
        return;
    }
    if (gcHolder.value.length >= props.data.dti_qty) {
        notification['warning']({
            message: 'Notification Title',
            description:
                'Holder Entry Has Reached the Max Quantity'
        });
        return;
    }
    gcHolder.value.push({ ...holderData.value });
    holderData.value = {
        lastname: '',
        firstname: '',
        mname: '',
        ext: '',
        address: '',
        voucher: '',
        bu: '',
    };
}

const clear = () => {
    holderData.value = {
        lastname: '',
        firstname: '',
        mname: '',
        ext: '',
        address: '',
        voucher: '',
        bu: '',
    };
}

const handleImage = (file) => {
    console.log(file)
    form.file = file;
}

const openModal = () => {
    open.value = true;
}

const columns = [
    {
        title: 'Last name',
        dataIndex: 'lastname',
        key: 'lastname',
    },
    {
        title: 'First Name',
        dataIndex: 'firstname',
        key: 'firstname',
    },
    {
        title: 'Middle Name',
        dataIndex: 'mname',
        key: 'mname',
    },
    {
        title: 'Name ext.',
        dataIndex: 'ext',
        key: 'ext',
    },
    {
        title: 'Address',
        dataIndex: 'address',
        key: 'address',
    },
    {
        title: 'Voucher',
        dataIndex: 'voucher',
        key: 'voucher',
    },
    {
        title: 'Business Unit',
        dataIndex: 'bu',
        key: 'bu',
    },

]


</script>
