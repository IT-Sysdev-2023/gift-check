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
                        <a-card size="small">
                            <a-row :gutter="[16, 16]">
                                <a-col :span="12" :key="index" v-for="(item, index) in props.data.dti_documents">
                                    <a-image style="height: 120px; width: 100%;" class="shadow-lg rounded-lg"
                                        :src="`/storage/${item.dti_fullpath}`" />
                                </a-col>
                            </a-row></a-card>

                    </a-form-item>
                </a-col>
                <a-col :span="8">
                    <a-form-item label="Company Name">
                        <a-input readonly :value="data.spcus_companyname" />
                    </a-form-item>
                    <a-form-item label="Account Name">
                        <a-input readonly :value="data.spcus_acctname" />
                    </a-form-item>
                    <a-form-item label="Payment Type">
                        <a-input readonly class="uppercase" :value="data.dti_paymenttype" />
                    </a-form-item>

                </a-col>
                <a-col :span="8">
                    <a-form-item label="Remarks">
                        <a-textarea readonly :value="data.dti_remarks" />
                    </a-form-item>
                    <div v-for="(item, key) in data.special_dti_gcrequest_items_has_many" :key="key">
                        <!-- {{ item }} -->
                        <a-row :gutter="[16, 16]">
                            <a-col :span="8">
                                <a-form-item label="Demomination">
                                    <a-input readonly :value="item.dti_denoms" />
                                </a-form-item>
                            </a-col>
                            <a-col :span="6">
                                <a-form-item label="Qty">
                                    <a-input readonly :value="item.dti_qty" />
                                </a-form-item>
                            </a-col>
                            <a-col :span="4">
                                <div class=" h-16 flex items-end">
                                    <a-button @click="openModal(item)" type="primary">
                                        <UserAddOutlined />
                                    </a-button>
                                </div>
                            </a-col>
                            <a-col :span="6">
                                <a-form-item label="# Holder" has-feedback
                                    :validate-status="form.errors.holders ? 'error' : ''" :help="form.errors.holders">
                                    <a-input readonly :value="checkExistence(item.tempId)" />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </div>
                    <a-form-item>
                        <div class="h-14 flex items-center">
                            <p class=" text-3xl">Total: {{ data.total }}</p>
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
                        <a-input placeholder="This field is required" v-model:value="holderData.lname" />
                    </a-form-item>
                    <a-form-item label="First Name">
                        <a-input placeholder="This field is required" v-model:value="holderData.fname" />
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
                            <a-table :pagination="false" size="small"
                                :dataSource="gcHolder.filter((data) => data.trid == holderSetup.tempId)"
                                :columns="columns" />
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
const props = defineProps({
    data: Object
})
const open = ref(false);
const gcHolder = ref([]);


const holderData = ref({
    lname: '',
    fname: '',
    mname: '',
    ext: '',
    address: '',
    voucher: '',
    bu: '',
})


const form = useForm({
    holders: computed(() => gcHolder.value),
    existingData: computed(() => props.data)
});



const handleSubmit = () => {
    form.post(route('custodian.dti_special_gcsubmit_dti_special_gc'), {
        onSuccess: (r) => {
            notification['success']({
                message: 'Success',
                description:
                    'Successfully Approved!',
            });
        },
        onError: () => {
            notification['error']({
                message: 'Error',
                description:
                    'Some Fields are missing or Error when Submitting',
            });
        }
    })
}

const idHolder = ref(1);

const checkExistence = (temp) => {
    console.log(temp);
    return gcHolder.value?.filter((data) => data.trid == temp).length ?? 0;
};
const handleAssign = () => {
    if (holderSetup.value.dti_qty > gcHolder.value.filter((data) => data.trid == holderSetup.value.tempId).length) {
        gcHolder.value.push({
            id: idHolder.value++,
            trid: holderSetup.value.tempId,
            fname: holderData.value.fname,
            lname: holderData.value.lname,
            mname: holderData.value.mname,
            ext: holderData.value.ext,
            address: holderData.value.address,
            voucher: holderData.value.voucher,
            bu: holderData.value.bu,
            denom: holderSetup.value.dti_denoms,
            reqid: holderSetup.value.dti_trid
        });

        notification['success']({
            message: 'Success',
            description:
                'Successfully Assigning Customer Employee!',
            placement: 'topLeft'
        });
    } else {
        notification['warning']({
            message: 'Maximum Reach',
            description:
                'Maximum limit reach assigning holder',
        });
    }

}

const clear = () => {
    holderData.value = {
        lname: '',
        fname: '',
        mname: '',
        ext: '',
        address: '',
        voucher: '',
        bu: '',
    };
}

const holderSetup = ref({});

const openModal = (data) => {
    holderSetup.value = data;
    open.value = true;
}

const columns = [
    {
        title: 'Last name',
        dataIndex: 'lname',
        key: 'lastname',
    },
    {
        title: 'First Name',
        dataIndex: 'fname',
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
