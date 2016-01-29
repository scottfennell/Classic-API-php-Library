package com.trackvia.api.client;

import java.util.List;
import java.util.ArrayList;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class DashboardsStrategy extends APIStrategy {
	
	private List<Map> dashboards = new ArrayList<Map> ();

	public List<Map> getDashboards() {
		return dashboards;
	}

	protected void setDashboards(List<Map> dashboards) {
		this.dashboards = dashboards;
	}

	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl("https://api.trackvia.com/dashboards", clientId, responseTypes);
		builder.put("access_token", this.accessToken);
		
		return builder;

	}

	@Override
	protected void postExecute(String responseBody) {
		List<Object> list = APIClient.getResponseAsList(responseBody);
		dashboards.clear();
		for ( Object dashboard : list) {
			dashboards.add( (Map) dashboard);
		}
	}

	public DashboardsStrategy(String accessToken) {
		setAccessToken(accessToken);
	}
}
