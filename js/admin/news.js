Ext.onReady(function () {
    var pageUrl = intelli.config.admin_url + '/news/news/';

    if (Ext.get('js-grid-placeholder')) {
        var urlParam = intelli.urlVal('status');

        intelli.news =
            {
                columns: [
                    'selection',
                    {name: 'title', title: _t('title'), width: 1, editor: 'text'},
                    {name: 'date_added', title: _t('date_added'), width: 170, editor: 'date'},
                    {name: 'date_modified', title: _t('date_modified'), width: 170, hidden: true},
                    'status',
                    'update',
                    'delete'
                ],
                storeParams: urlParam ? {status: urlParam} : null,
            };

        intelli.news = new IntelliGrid(intelli.news, false);
        intelli.news.toolbar = Ext.create('Ext.Toolbar', {
            items: [
                {
                    emptyText: _t('text'),
                    name: 'text',
                    listeners: intelli.gridHelper.listener.specialKey,
                    xtype: 'textfield'
                }, {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    id: 'fltStatus',
                    name: 'status',
                    store: intelli.news.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    xtype: 'combo'
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.news);
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function () {
                        intelli.gridHelper.search(intelli.news, true);
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }
            ]
        });

        if (urlParam) {
            Ext.getCmp('fltStatus').setValue(urlParam);
        }

        intelli.news.init();
    }

    intelli.titleCache = '';
    intelli.fillUrlBox = function () {
        var id = $('input[name="id"]').val();
        var slug = $('#field_title_slug').val();
        var title = ('' == slug ? $('input:first', '#title_fieldzone').val() : slug);
        var cache = title + '%%';

        if ('' !== title && intelli.titleCache != cache) {
            $.get(pageUrl + 'slug.json', {id: id, title: title}, function (response) {
                if ('' !== response.data) {
                    $('#title_url').text(response.data);
                    $('#title_box').fadeIn();
                }
            });
        }
console.log(id);
        intelli.titleCache = cache;
    };

    $(function () {
        $('#title_fieldzone input:first, #field_title_slug').blur(intelli.fillUrlBox).blur();
    });
});