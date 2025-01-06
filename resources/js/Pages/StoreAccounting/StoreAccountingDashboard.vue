<template>
    <a-card>

        <a-card style="font-weight: bold;" title="EOD LIST"></a-card>

        <!-- <div class="input-wrapper">
            <input type="search" placeholder="Input search here..." name="text" class="input" v-model="searchTerm" />
        </div> -->


        <div style=" margin-left: 70%; margin-top: 10px;">
            <span>
                <a-input-search allow-clear v-model:value="searchTerm" placeholder="Input search here!" enter-button
                    style="width:90%;" />
            </span>
        </div>
        <div style="margin-top: 10px;">
            <a-table :data-source="data.data" :columns="columns" :pagination="false" size="small"
                style="border: 1px solid #f5f5f5;">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button title="view" @click="viewEODList(record)"
                            style="color:white; background-color: #1e90ff;">
                            <EyeOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <!-- {{ data }} -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import { DatabaseOutlined } from '@ant-design/icons-vue';
// import Pagination from '@/Components/Pagination.vue';
import { notification } from 'ant-design-vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        pagination: Number,
        search: String
    },

    data() {
        return {
            open: false,
            message: '',
            dataForSelectEntries: {
                select_entries: this.pagination
            },
            searchTerm: this.search,
            buttonPosition: { top: 0, left: 0 },
            selectedEODDate: '',
            columns: [
                {
                    title: 'Stores',
                    dataIndex: 'store_name',
                    sorter: (a, b) => (a.store_name || '').localeCompare(b.store_name || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'EOD By',
                    dataIndex: 'fullname',
                    sorter: (a, b) => (a.fullname || '').localeCompare(b.fullname || '', undefined, { sensitivity: 'base' }),


                },
                {
                    title: 'Date Time',
                    dataIndex: 'steod_datetime',
                    sorter: (a, b) => (a.steod_datetime || '').localeCompare(b.steod_datetime || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'View',
                    dataIndex: 'action'
                }

            ],
        }


    },
    watch: {
        searchTerm(search) {
            console.log(search);
            const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if(searchValidation.test(search)){
                const openNotificationWithIcon = (type)=>{
                    notification[type]({
                        message: 'Invalid input',
                        description: 'Search contains invalid symbols or emojis',
                        placement: 'topRight'
                    });
                };
                 openNotificationWithIcon('warning');
                    return;
            }
            this.$inertia.get(route('storeaccounting.dashboard'), {
                data: search
            }, {
                preserveState: true,
                preserveScroll: true
            })
        }
    },
    methods: {
        dashboardSelectEntries(entries) {
            console.log(entries);
            this.$inertia.get(route('storeaccounting.dashboard'), {
                value: entries
            }, {
                preserveState: true,
                preserveScroll: true
            })



        },
        viewEODList(rec) {
            this.selectedEODDate = rec.steod_datetime
            this.$inertia.get(route('storeaccounting.storeeod', rec.steod_id), {
                eodDate: this.selectedEODDate

            })

        },

    }

}




</script>
<style scoped>
/* From Uiverse.io by adamgiebl */
.input-wrapper input {
    background-color: whitesmoke;
    border: none;
    padding: 1rem;
    font-size: 1rem;
    width: 16em;
    border-radius: 2rem;
    color: black;
    box-shadow: 0 0.4rem #1e90ff;
    cursor: pointer;
    margin-top: 10px;
    margin-left: 70%;
}

.input-wrapper input:focus {
    outline-color: whitesmoke;
}
</style>
