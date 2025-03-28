<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('admin.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item>
                {{ title }}
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                {{ selectedTab }}
            </a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5" title="Customer Setup">
            <!-- regular modal section  -->
            <a-modal v-model:open="regularModal" @ok="updateRegularCustomer" title="Regular Customer Update">
                <div class="mt-10">
                    <a-form-item label="First Name">
                        <a-input v-model:value="regularUpdateData.cus_fname" />
                    </a-form-item>
                    <a-form-item label="Last Name">
                        <a-input v-model:value="regularUpdateData.cus_lname" />
                    </a-form-item>
                    <a-form-item label="Store Registered">
                        <a-select v-model:value="regularUpdateData.storeRegistered">
                            <a-select-option v-for="item in props.store" :key="item.store_id" :value="item.store_id">
                                {{ item.store_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </div>
            </a-modal>
            <a-tabs @change="activeTab" v-model:activeKey="activeKey" type="card">
                <a-tab-pane key="1" tab="Regular Customer">
                    <div>
                        <a-input-search allow-clear v-model:value="regularCustomerData" @change="regularSearchFunction"
                            enter-button placeholder="Input search here..." class="mb-5 w-1/4 float-right" />
                    </div>
                    <!-- regular table section  -->
                    <div v-if="props.regularCustomer">
                        <a-table :data-source="props.regularCustomer.data" :columns="regularCustomerColumns"
                            size="small" :pagination="false">
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button @click="openRegularModal(record)" type="primary"
                                        class="bg-green-600 text-white">
                                        <EditOutlined />
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                    </div>
                    <div v-else>
                        <a-empty />
                    </div>
                    <pagination :datarecords="props.regularCustomer" class="mt-5" />
                </a-tab-pane>
                <a-tab-pane key="2" tab="Institutional Customer">
                    <!--institutional modal section  -->
                    <div>
                        <a-modal v-model:open="institutionalModal" @ok="updateInstitutional"
                            title="Institutional Customer Update">
                            <div class="mt-10">
                                <a-form-item label="Name">
                                    <a-input v-model:value="institutionalUpdateData.ins_name" />
                                </a-form-item>
                                <a-form-item label="Customer Type">
                                    <a-select v-model:value="institutionalUpdateData.ins_custype">
                                        <a-select-option value="internal">
                                            Internal
                                        </a-select-option>
                                        <a-select-option value="external">
                                            External
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                                <a-form-item label="GC Type">
                                    <a-select v-model:value="institutionalUpdateData.gcType">
                                        <a-select-option value="1">
                                            Regular
                                        </a-select-option>
                                        <a-select-option value="2">
                                            Special
                                        </a-select-option>
                                        <a-select-option value="3">
                                            Special External
                                        </a-select-option>
                                        <a-select-option value="4">
                                            Promo
                                        </a-select-option>
                                        <a-select-option value="5">
                                            Supplier GC
                                        </a-select-option>
                                        <a-select-option value="6">
                                            Beam and Go
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                                <a-form-item label="Status">
                                    <a-select v-model:value="institutionalUpdateData.ins_status">
                                        <a-select-option value="active">
                                            Active
                                        </a-select-option>
                                        <a-select-option value="inactive">
                                            Inactive
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </div>
                        </a-modal>
                    </div>
                    <div>
                        <a-input-search allow-clear v-model:value="institutionalData"
                            @change="institutionalSearchFunction" enter-button placeholder="Input search here..."
                            class="mb-5 w-1/4 float-right" />
                    </div>
                    <!--institutional table section  -->
                    <div v-if="props.institutional">
                        <a-table :data-source="props.institutional.data" :columns="props.institutinalColumns"
                            size="small" :pagination="false">
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button @click="openInstitutionalModal(record)" type="primary"
                                        class="bg-green-600 text-white">
                                        <EditOutlined />
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                    </div>
                    <div v-else>
                        <a-empty />
                    </div>
                    <pagination :datarecords="props.institutional" class="mt-5" />
                </a-tab-pane>
                <a-tab-pane key="3" tab="Special Customer">
                    <!-- special modal section  -->
                    <div>

                        <a-modal v-model:open="specialModal" @ok="updateSpecialCustomer"
                            title="Special Customer Update">
                            <div>
                                <a-form-item label="Company Name">
                                    <a-input v-model:value="specialUpdateData.spcus_companyname" />
                                </a-form-item>
                                <a-form-item label="Account Name">
                                    <a-input v-model:value="specialUpdateData.spcus_acctname" />
                                </a-form-item>
                                <a-form-item label="Customer Address">
                                    <a-input v-model:value="specialUpdateData.spcus_address" />
                                </a-form-item>
                                <a-form-item label="Contact Person">
                                    <a-input v-model:value="specialUpdateData.spcus_cperson" />
                                </a-form-item>
                                <a-form-item label="Contact Number">
                                    <a-input v-model:value="specialUpdateData.spcus_cnumber" />
                                </a-form-item>
                                <a-form-item label="Customer Type">
                                    <a-select v-model:value="specialUpdateData.special_type">
                                        <a-select-option value="1">
                                            Internal
                                        </a-select-option>
                                        <a-select-option value="2">
                                            External
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </div>
                        </a-modal>
                    </div>
                    <div>
                        <a-input-search allow-clear v-model:value="specialCustomerData"
                            @change="specialCustomerSearchFunction" enter-button placeholder="Input search here..."
                            class="mb-5 w-1/4 float-right" />
                    </div>
                    <!-- special table section  -->
                    <div v-if="props.specialCustomer">
                        <a-table :data-source="props.specialCustomer.data" :columns="specialCustomerColumns"
                            size="small" :pagination="false">
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'action'">
                                    <a-button @click="openSpecialModal(record)" type="primary"
                                        class="bg-green-600 text-white">
                                        <EditOutlined />
                                    </a-button>
                                </template>
                            </template>
                        </a-table>
                    </div>
                    <div v-else>
                        <a-empty />
                    </div>
                    <pagination :datarecords="props.specialCustomer" class="mt-5" />
                </a-tab-pane>
            </a-tabs>
        </a-card>
        <!-- {{ store }} -->
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    institutional: {
        data: Institutional
    },
    institutinalColumns,
    regularCustomer: {
        data: RegularCustomer
    },
    institutionalSearch: string,
    regularCustomerColumns,
    regularSearch: string
    specialCustomer: {
        data: SpecialCustomer
    },
    specialCustomerSearch: string,
    specialCustomerColumns,
    activeTabs: string,
    store
}>();

const title = ref<string>('Customer Setup');
const activeKey = ref<string>(props.activeTabs ? props.activeTabs : '1');
const activeTab = (key: string) => {
    activeKey.value = key;
};
// active tab title
const tabTitles: Record<string, string> = {
    '1': 'Regular Customer',
    '2': 'Institutional Customer',
    '3': 'Special Customer'
};
const selectedTab = computed(() =>
    tabTitles[activeKey.value]);

interface RegularCustomer {
    cus_id: number,
    cus_fname: string,
    cus_lname: string,
    cus_register_at: string,
    cus_store_register: number,
    cus_register_by: number,
    errors: {
        cus_fname: string,
        cus_lname: string,
        cus_store_register: string
    }

};
interface Institutional {
    ins_id: number,
    ins_name: string,
    ins_status: string,
    ins_custype: string,
    ins_gctype: string,
    ins_date_created: string,
    ins_by: number
};

interface SpecialCustomer {
    spcus_id: number,
    spcus_companyname: string,
    spcus_acctname: string,
    spcus_address: string,
    spcus_cperson: string,
    spcus_cnumber: number,
    spcus_type: number,

}

// Regular Customer search function
const regularCustomerData = ref<string>(props.regularSearch);
const regularSearchFunction = () => {
    router.get(route('admin.masterfile.customer.setup'), {
        regularSearch: regularCustomerData.value,
        activeKey: activeKey.value
    }, {
        preserveState: true
    });
};
// Regular Customer Modal
const regularModal = ref<boolean>(false);
const regularUpdateData = ref(null);
const openRegularModal = (record: RegularCustomer) => {
    regularUpdateData.value = record;
    regularModal.value = true;
};

// submit function
const updateRegularCustomer = () => {
    router.post(route('admin.masterfile.updateCustomerStoreRegister'), {
        id: regularUpdateData.value.cus_id,
        firstname: regularUpdateData.value.cus_fname,
        lastname: regularUpdateData.value.cus_lname,
        store: regularUpdateData.value.storeRegistered
    }, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                regularModal.value = false;
            } else if (page.props.flash.error) {
                notification.warning({
                    message: 'Warning',
                    description: page.props.flash.error
                });
            }
        },
        onError: () => {
            notification.error({
                message: 'Error',
                description: 'Please fill all required fields'
            });
        }
    });
};

// Institutional Customer search function
const institutionalData = ref<string>(props.institutionalSearch);
const institutionalSearchFunction = () => {
    router.get(route('admin.masterfile.customer.setup'), {
        institutionalSearch: institutionalData.value,
        activeKey: activeKey.value
    }, {
        preserveState: true
    });
};
// Institutional Customer Modal
const institutionalUpdateData = ref(null);
const institutionalModal = ref<boolean>(false);
const openInstitutionalModal = (record: Institutional) => {
    institutionalUpdateData.value = record;
    institutionalModal.value = true;

}
// submit function
const updateInstitutional = () => {
    router.post(route('admin.masterfile.UpdateInstituteCustomer'), {
        id: institutionalUpdateData.value.ins_id,
        name: institutionalUpdateData.value.ins_name,
        customerType: institutionalUpdateData.value.ins_custype,
        gcType: institutionalUpdateData.value.gcType,
        status: institutionalUpdateData.value.ins_status
    }, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                institutionalModal.value = false;
            } else if (page.props.flash.error) {
                notification.warning({
                    message: 'Warning',
                    description: page.props.flash.error
                });
            }
        },
        onError: () => {
            notification.error({
                message: 'Error',
                description: 'Please fill all required fields'
            });
        }
    });
};

// Special Customer search function
const specialCustomerData = ref<string>(props.specialCustomerSearch);
const specialCustomerSearchFunction = () => {
    router.get(route('admin.masterfile.customer.setup'), {
        specialCustomerSearch: specialCustomerData.value,
        activeKey: activeKey.value
    }, {
        preserveState: true
    });
};
// Special Customer Modal
const specialModal = ref<boolean>(false);
const specialUpdateData = ref(null);
const openSpecialModal = (record: SpecialCustomer) => {
    specialModal.value = true;
    specialUpdateData.value = record;

}
// special submit function
const updateSpecialCustomer = () => {
    router.post(route('admin.masterfile.updateSpecialCustomer'), {
        id: specialUpdateData.value.spcus_id,
        companyname: specialUpdateData.value.spcus_companyname,
        acctname: specialUpdateData.value.spcus_acctname,
        address: specialUpdateData.value.spcus_address,
        cperson: specialUpdateData.value.spcus_cperson,
        cnumber: specialUpdateData.value.spcus_cnumber,
        type: specialUpdateData.value.special_type
    }, {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                notification.success({
                    message: 'Success',
                    description: page.props.flash.success
                });
                specialModal.value = false;
            } else if (page.props.flash.error) {
                notification.warning({
                    message: 'Warning',
                    description: page.props.flash.error
                });
            }
        },
        onError: () => {
            notification.error({
                message: 'Error',
                description: 'Please fill all required fields'
            });
        }
    });
};

</script>
