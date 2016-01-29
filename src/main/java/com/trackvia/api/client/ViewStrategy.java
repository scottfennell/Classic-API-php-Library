package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;


import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class ViewStrategy extends APIStrategy {
	
	private Long viewId = new Long(0);
	private String name = new String();
	private List<Map> rows = new ArrayList<Map> ();	
	private Integer limit = 0;
	private Integer page = 0;
	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		ArrayList<String> responseTypes = new ArrayList<String> ();
		AuthorizationRequestUrl builder;
		responseTypes.add("token");
		
		if (getViewId() != null) {
			builder = new AuthorizationRequestUrl(super.baseUrl + "/views/" + getViewId() + ".json", clientId, responseTypes);
		} else {
			builder = new AuthorizationRequestUrl(super.baseUrl + "/views.json", clientId, responseTypes);
		}
		builder.put("access_token", this.accessToken);
		
		if (this.limit > 0) {
			builder.put("limit", this.limit);
		}
		
		if (this.page > 0) {
			builder.put("page", this.page);
		}
		
		return builder;
	}

	@Override
	protected void postExecute(String responseBody) {
		Map<String, Object> map = APIClient.getResponseAsMap(responseBody);
		
		setName( (String)map.get("name") );
		setRows( ((List<Map>)map.get("rows")) );

	}

	public ViewStrategy(String accessToken, Long viewId) {
		setAccessToken(accessToken);
		setViewId(viewId);
	}

	public Long getViewId() {
		return viewId;
	}

	private void setViewId(Long viewId) {
		this.viewId = viewId;
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
}
