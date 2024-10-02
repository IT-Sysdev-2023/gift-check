<template>

    <Head title="Manage Supplier" />
    <a-card>
        <a-card class="mb-2" title="Manage Supplier"></a-card>
        <div class="flex justify-end">
            <a-button @click="showModal">
                <PlusOutlined /> Add New Supplier
            </a-button>

            <a-modal v-model:open="open" title="Add New Supplier" :footer="false">
                <a-form>
                    <a-form-item label="Company Name" has-feedback :help="error.gcs_companyname"
                        :validate-status="error.gcs_companyname ? 'error' : ''">
                        <a-input v-model:value="form.gcs_companyname" @change="() => error.gcs_companyname = ''" />
                    </a-form-item>
                    <a-form-item label="Account Name" has-feedback :help="error.gcs_accountname"
                        :validate-status="error.gcs_accountname ? 'error' : ''">
                        <a-input v-model:value="form.gcs_accountname" @change="() => error.gcs_accountname = ''" />
                    </a-form-item>
                    <a-form-item label="Contact Person" has-feedback :help="error.gcs_contactperson"
                        :validate-status="error.gcs_contactperson ? 'error' : ''">
                        <a-input v-model:value="form.gcs_contactperson" @change="() => error.gcs_contactperson = ''" />
                    </a-form-item>
                    <a-form-item label="Contact Number" has-feedback :help="error.gcs_contactnumber"
                        :validate-status="error.gcs_contactnumber ? 'error' : ''">
                        <a-input v-model:value="form.gcs_contactnumber" @change="() => error.gcs_contactnumber = ''" />
                    </a-form-item>
                    <a-form-item label="Address" has-feedback :help="error.gcs_address"
                        :validate-status="error.gcs_address ? 'error' : ''">
                        <a-input v-model:value="form.gcs_address" @change="() => error.gcs_address = ''" />
                    </a-form-item>

                    <div>
                        <div class="flex justify-end">
                            <a-button @click="addSupplier" :loading="form.processing">
                                <template #icon>
                                    <StepForwardOutlined />
                                </template>
                                Submit
                            </a-button>
                        </div>
                    </div>
                </a-form>
            </a-modal>
        </div>
        <div class="flex justify-end">
            <a-input-search class="mt-5 mb-5" v-model:value="search" placeholder="input search text here."
                style="width: 300px" @search="onSearch" />
        </div>

        <a-table :dataSource="data.data" :columns="columns" :pagination="false" bordered>
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">

                    <a-button @click="updateStatus(record.gcs_id)" size="small"
                        :type="record.gcs_status == '1' ? 'primary' : 'ghost'">{{ record.gcs_status == 1 ? 'Active' :
                            'Inactive' }}</a-button>
                </template>
            </template>
        </a-table>

        <pagination class="mt-5" :datarecords="data" />
    </a-card>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import debounce from "lodash/debounce";
import { PlusOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import { useForm } from "@inertiajs/vue3";

export default {
    layout: AuthenticatedLayout,
    PlusOutlined,
    props: {
        data: Object,
        columns: Array,
    },
    data() {
        return {
            search: '',
            open: false,
            error: {},
            form: useForm({
                gcs_companyname: '',
                gcs_accountname: '',
                gcs_contactperson: '',
                gcs_contactnumber: '',
                gcs_address: ''
            }),
        }
    },
    methods: {
        showModal() {
            this.open = true;
        },
        handleOk() {
            this.open = false;
        },
        handleCancel() {
            this.open = false;
        },

        addSupplier() {
            this.form.post(route('marketing.manage-supplier.add.supplier'), {
                onSuccess: (response) => {
                    if (response.props.flash.type === 'success') {
                        this.form.reset()
                    }
                    notification[response.props.flash.type]({
                        message: response.props.flash.msg,
                        description: response.props.flash.description,
                    })
                },
                onError: (response) => {
                    this.error = response
                }
            })
        },


        updateStatus(id) {
            this.$inertia.get(route('marketing.manage-supplier.status.supplier'), {
                id: id
            }, {
                onSuccess: (response) => {
                    notification[response.props.flash.type]({
                        message: response.props.flash.msg,
                        description: response.props.flash.description,
                    })
                }
            });
        },
        onSearch() {

        }
    },
}
</script>
