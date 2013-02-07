package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class DashboardStrategy extends APIStrategy {
	
	private Long dashboardId = new Long(0);
	private String name = new String();
	private List<Map> rows = new ArrayList<Map> ();

	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl("https://api.trackvia.com/dashboards/" + getDashboardId() + ".json", clientId, responseTypes);
		builder.put("access_token", this.accessToken);
		
		return builder;
	}

	@Override
	protected void postExecute(String responseBody) {
		Map<String, Object> map = APIClient.getResponseAsMap(responseBody);
		
		setName( (String)map.get("name") );
		setRows( ((List<Map>)map.get("rows")) );

	}
	
	public DashboardStrategy(String accessToken, Long dashboardId) {
		setAccessToken(accessToken);
		setDashboardId(dashboardId);
	}

	public Long getDashboardId() {
		return dashboardId;
	}

	private void setDashboardId(Long dashboardId) {
		this.dashboardId = dashboardId;
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
