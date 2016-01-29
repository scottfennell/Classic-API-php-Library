package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class TablesStrategy extends APIStrategy {
	
	private Long tableId = new Long(0);
	private String name = new String();
	private String recordLabel = new String();
	private String recordPlural = new String();
	private List<Map> views = new ArrayList<Map> ();
	private List<Map> columnDefinitions = new ArrayList<Map> ();

	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder =  new AuthorizationRequestUrl("https://api.trackvia.com/tables/" + getTableId().toString() + ".json", clientId, responseTypes);
		builder.put("access_token", getAccessToken());
		
		return builder;		
	}

	@Override
	protected void postExecute(String responseBody) {
		Map<String, Object> map = APIClient.getResponseAsMap(responseBody);
		
		setName((String)map.get("name"));
		setRecordLabel((String)map.get("record_label"));
		setRecordPlural((String)map.get("record_plural"));
		setViews( (List<Map>)map.get("views") );
		setColumnDefinitions( (List<Map>)map.get("coldefs"));

	}
	
	public TablesStrategy(String accessToken, Long tableId) {
		setAccessToken(accessToken);
		setTableId(tableId);
		
	}
	
	private String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getRecordLabel() {
		return recordLabel;
	}

	private void setRecordLabel(String recordLabel) {
		this.recordLabel = recordLabel;
	}

	public String getRecordPlural() {
		return recordPlural;
	}

	private void setRecordPlural(String recordPlural) {
		this.recordPlural = recordPlural;
	}

	public List<Map> getViews() {
		return views;
	}

	private void setViews(List<Map> views) {
		this.views = views;
	}

	public List<Map> getColumnDefinitions() {
		return columnDefinitions;
	}

	private void setColumnDefinitions(List<Map> columnDefinitions) {
		this.columnDefinitions = columnDefinitions;
	}

	

	public Long getTableId() {
		return tableId;
	}

	private void setTableId(Long tableId) {
		this.tableId = tableId;
	}

}
