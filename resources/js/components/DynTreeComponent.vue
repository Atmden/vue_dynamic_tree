<template>
    <div class="row">
        <div class="col" id="tree">
            <button class="btn-sm btn-green" @click="collapseAll">Раскрыть весь список</button>
            <button class="btn-sm btn-green" @click="expandAll">Свернуть весь список</button>
            <v-treeview
                    ref="treereferals"
                    :items="items"
                    :load-children="loadTree"
                    :active.sync="selectedItem"
                    :active="active"
                    return-object
                    item-key="id"
                    rounded
                    hoverable
                    transition
                    activatable
                    @update:active="test"
            >
                <template slot="prepend" slot-scope="{ item, open}">
                    <v-icon>
                        mdi-account
                    </v-icon>
                </template>
                <template slot="label" slot-scope="{item}">
                    {{ item.family }} {{ item.fname }}
                </template>
            </v-treeview>

        </div>
        <div class="col" ref="colvcard">
            <affix class="scrollaffix-sidebar" relative-element-selector="#tree"
                   :offset="{ top: 30, bottom: 40 }" :scroll-affix="true" :enabled="affix"
                   :style="{ width: vcardwidth + 'px'}"
            >
                <v-card v-if="selectedItem[0]" ref="vcard">
                    <v-card-title class="text-center"><b>Информация о пользователе</b></v-card-title>
                    <v-card-text>
                        <b>Ф.И.О.: </b>{{ selectedItem[0].family }} {{ selectedItem[0].fname
                        }} {{ selectedItem[0].fathername }}<br>
                        <b>Регион: </b><span v-if="selectedItem[0].region !== null">{{ selectedItem[0].region.name }}</span>
                                        <span v-else>регион не указан!</span>
                        <br>
                        <b>Доход: </b> <img src="/images/loader.gif" v-show="loading" alt=""> <span v-show="!loading"> {{ income
                        }} </span>
                        <!--<div v-show="this.income == null" ><img src="/images/loader.gif" alt=""> </div>-->
                        <br>

                    </v-card-text>
                </v-card>
            </affix>
        </div>
    </div>
</template>
<style>
    .v-treeview-node__content {
        cursor: pointer;
    }

    .v-card__title {
        display: block;
        padding: 0px;
    }

    .row {
        display: flex;
    }

    .row.col {
        flex-basis: 50%;
    }

    .btn-sm {
        margin-bottom: 5px;
    }

    @media (max-width: 768px) {
        .v-treeview-node__label {
            font-size: 11px;
        }

        .row {
            display: block;
        }

        .row .col {
            margin-top: 25px;
        }
    }
</style>
<script>

    import {Affix} from 'vue-affix';

    export default {

        components: {
            Affix,
        },
        props: {
            referalsgl: {default: ''},
        },
        methods: {
            calcStickyWidth() {
                this.vcardwidth = this.$refs.colvcard.clientWidth-30;
            },
            screensize() {
                this.size = window.innerWidth;
                return this.size;
            },
            async loadTree(item) {
                return axios.get('/cabinet/dyntree/' + item.id)
                    .then(response => {
                        if (response.data.success) {
                            let tmpItem = response.data.children.map(childItem => {
                                if (childItem.parent_count) {
                                    childItem.children = [];
                                }
                                return childItem;
                            });
                            item.children.push(...tmpItem);
                        }
                    });
            },
            test(item) {
                if (item.length) {
                    this.showCard(this.selectedItem[0].id);
                    this.calcStickyWidth();
                }
            },
            showCard(item) {
                this.loading = true;
                axios
                    .get('/cabinet/income/' + item)
                    .then(response => {
                        this.income = response.data;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            collapseAll() {
                this.$refs.treereferals.updateAll(true);
            },
            expandAll() {
                this.$refs.treereferals.updateAll(false);
            }
        },
        data() {
            return {
                items: this.referalsgl,
                treeData: [],
                referals: this.referalsgl,
                selectedItem: [],
                income: null,
                loading: false,
                errored: false,
                active: [],
                affix: true,
                vcardwidth:0,
                size: ''
            }
        },
        mounted() {
            this.calcStickyWidth();
            if (this.screensize() <= 768 ) {
                this.affix = false;
            }
        },
    }

</script>
