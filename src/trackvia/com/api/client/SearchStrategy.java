package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.JsonMappingException;
import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class SearchStrategy extends APIStrategy {
	
	private Long tableId = new Long(0);
	private String name = new String();
	private List<Map> rows = new ArrayList<Map> ();	
	private String searchTerm = new String();
	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		String url = "";
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl(super.baseUrl + "/search/" + this.getTableId().toString() + "/" + this.searchTerm + ".json", clientId, responseTypes);
		builder.put("access_token", this.accessToken);
		
		return builder;
	}

	@Override
	protected void postExecute(String responseBody) {

	}

	public SearchStrategy(String accessToken, Long tableId, String searchTerm) {
		setAccessToken(accessToken);
		setTableId(tableId);
		if(null != searchTerm) {
			this.setSearchTerm(searchTerm);
		}
	}

	public Long getTableId() {
		return tableId;
	}

	private void setTableId(Long tableId) {
		this.tableId = tableId;
	}

	public String getName() {
		return name;
	}

	private void setName(String name) {
		this.name = name;
	}
	
	public List<Map> getRows() {
		return rows;
	}

	private void setRows(List<Map> rows) {
		this.rows = rows;
	}	
	
	private void setSearchTerm(String searchTerm) {
		this.searchTerm = searchTerm;
	}
}
