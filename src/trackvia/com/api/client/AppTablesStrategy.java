package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class AppTablesStrategy extends APIStrategy {

	private Long appId = new Long(0);
	private String name = new String();
	private List<Map> tables = new ArrayList<Map> ();
	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder =  new AuthorizationRequestUrl("https://api.trackvia.com/apps/" + getAppId().toString() + ".json", clientId, responseTypes);
		builder.put("access_token", getAccessToken());
		
		return builder;		
	}

	@Override
	protected void postExecute(String responseBody) {
		// TODO Auto-generated method stub
		
		Map<String, Object> map = APIClient.getResponseAsMap(responseBody);
		setName((String)map.get("name"));
		setTables((List<Map>)map.get("tables"));

	}
	
	public AppTablesStrategy(String accessToken, Long id) {
		setAccessToken(accessToken);
		setAppId(id);
	}

	public Long getAppId() {
		return appId;
	}

	protected void setAppId(Long appId) {
		this.appId = appId;
	}

	public String getName() {
		return name;
	}

	private void setName(String name) {
		this.name = name;
	}

	public List<Map> getTables() {
		return tables;
	}

	private void setTables(List<Map> tables) {
		this.tables = tables;
	}

}
