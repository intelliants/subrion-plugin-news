Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/news/';

	if (Ext.get('js-grid-placeholder'))
	{
		var urlParam = intelli.urlVal('status');

		intelli.news =
		{
			columns: [
				'selection',
				{name: 'title', title: _t('title'), width: 2, editor: 'text'},
				{name: 'alias', title: _t('alias'), width: 220},
				{name: 'owner', title: _t('owner'), width: 150},
				'status',
				{name: 'date', title: _t('date'), width: 120, editor: 'date'},
				'update',
				'delete'
			],
			storeParams: urlParam ? {status: urlParam} : null,
			url: pageUrl
		};

		intelli.news = new IntelliGrid(intelli.news, false);
		intelli.news.toolbar = Ext.create('Ext.Toolbar', {items:
		[
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
				emptyText: _t('owner'),
				listeners: intelli.gridHelper.listener.specialKey,
				name: 'owner',
				width: 150,
				xtype: 'textfield'
		}, {
				handler: function(){intelli.gridHelper.search(intelli.news);},
				id: 'fltBtn',
				text: '<i class="i-search"></i> ' + _t('search')
		}, {
				handler: function(){intelli.gridHelper.search(intelli.news, true);},
				text: '<i class="i-close"></i> ' + _t('reset')
			}
		]});

		if (urlParam)
		{
			Ext.getCmp('fltStatus').setValue(urlParam);
		}

		intelli.news.init();
	}
	else
	{
		$('#input-title, #input-alias').on('blur', function()
		{
			var alias = $('#input-alias').val();
			var title = alias != '' ? alias : $('#input-title').val();

			if ('' != title)
			{
				$.get(pageUrl + 'read.json', {get: 'alias', title: title}, function(data)
				{
					if ('' != data.url)
					{
						$('#title_url').text(data.url);
						$('#title_box').fadeIn();
					}
					else
					{
						$('#title_box').hide();
					}
				});
			}
			else
			{
				$('#title_box').hide();
			}
		});
	}
});