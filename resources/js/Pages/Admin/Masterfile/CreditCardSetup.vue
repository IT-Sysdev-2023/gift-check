<template>
    <div class="creditcard-button">
        <a-button class="creditcard-input"
            @click="() => addCreditCard = true">
            <PlusOutlined /> Add New Credit Card
        </a-button>
    </div>

    <div class="creditcard-search-button">
        Search:
        <a-input class="creditcard-search-input" allow-clear v-model:value="searchTerm" placeholder="Input search here!" enter-button="Search"
            size="medium" />
    </div>

    <a-title style="font-size: 20px; display: flex; align-items: center; color:#0286df;">
        <CreditCardFilled style="margin-right: 8px; color:#0286df;" />
        Credit Card Setup
    </a-title>

    <span style="font-weight: bold;">
        Show
        <a-select id="select_entries" v-model:value="dataForSelectEntries.select_entries" @change="changeSelectEntries"
            style="margin-top: 10px; background-color: #0286df; border: 1px solid #0286df" placeholder="10">
            <a-select-option value="10">10</a-select-option>
            <a-select-option value="25">25</a-select-option>
            <a-select-option value="50">50</a-select-option>
            <a-select-option value="100">100</a-select-option>
            <a-select-option value="100">all</a-select-option>
        </a-select>
        entries
    </span>
    <div style="background-color: #dcdcdc;">
        <a-table :columns="columns" :data-source="data.data" :pagination="false" style="margin-top: 10px">
        </a-table>
        <pagination :datarecords="data" class="mt-5" />
    </div>
    <a-modal v-model:open="addCreditCard" @ok="handleOk">
        <span style="color: #0286df; font-size: 17px;">
            <CreditCardOutlined style="margin-right: 8px; " />
            Add New Credit Card
        </span>


        <a-form-item for="ccard_name" :validate-status="form.errors.ccard_name ? 'error' : ''"
            :help="form.errors.ccard_name" style="margin-top: 10px;">
            Credit Name:
            <a-input allow-clear v-model:value="form.ccard_name" placeholder="Credit Card Name" />
        </a-form-item>
    </a-modal>
<!-- {{ data }} -->
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { FormOutlined, DeleteOutlined, PlusSquareOutlined, UserOutlined, UnlockTwoTone, CloseCircleTwoTone, AppstoreTwoTone } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
export default {
    layout: AuthenticatedLayout,
    components: { FormOutlined, DeleteOutlined, PlusSquareOutlined, UserOutlined, UnlockTwoTone, CloseCircleTwoTone, AppstoreTwoTone },
    
    props: {
        data: Object,
        search: String,
        value: String

    },
    data() {
        return {
            dataForSelectEntries: {
                select_entries: this.value
            },
            searchTerm: this.search,

            form: this.$inertia.form({
                ccard_name: ''
            }),

            addNewCustomerModal: false,
            addCreditCard: false,
            columns: [
                {
                    title: 'Credit Card',
                    dataIndex: 'ccard_name',
                    sorter: (a, b) => {
                        return a.ccard_name.charAt(0).toUpperCase().localeCompare(b.ccard_name.charAt(0).toUpperCase());
                    }
                }, {
                    title: 'Date Created',
                    dataIndex: 'ccard_created'
                },
                {
                    title: 'Created By',
                    dataIndex: 'ccard_by'
                },
                {
                    title: 'Status',
                    dataIndex: 'ccard_status'
                },
            ]
        }
    },
    watch: {
        searchTerm(newVal) {

            console.log(newVal);
            this.$inertia.get(route('admin.masterfile.creditCardSetup'), {
                data: newVal
            }, {
                preserveState: true
            });
        }
    },
    methods: {
        handleOk() {
            // alert(1)
            this.form.get(route('admin.masterfile.saveCreditCard'), {
                preserveState: true,
                onSuccess: ({ props }) => {

                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: 'Added successfully!'
                        });
                        this.addCreditCard = false,
                            this.$inertia.get(route('Admin/Masterfile/CreditCardSetup'))
                    }
                    else if (props.flash.error) {
                        notification.error({
                            message: props.flash.error,
                            description: 'Failed adding Credit Card!'
                        });
                    }
                },
            })

        },
        changeSelectEntries(value) {
            console.log(value);
            this.$inertia.get(route('admin.masterfile.creditCardSetup'), {
                value: value
            }, {
                preserveScroll: true,
                preserveState: true
            })
        }
    }

}
</script>
<style>
.creditcard-button {
    text-align: right;
}

.creditcard-input {
    background-color: #0286df;
    color: white;
    margin-right: 16%;
}

.creditcard-search-button {
    font-weight: bold;
    text-align: right;
}

.creditcard-search-input {
    border: 1px solid #0286df;
    width: 20%;
    margin-right: 10%;
    min-width: 110px;
    margin-top: 1%;
}
</style>
