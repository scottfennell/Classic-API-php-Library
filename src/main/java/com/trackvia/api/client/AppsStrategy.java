package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class AppsStrategy extends APIStrategy {
	
	private List<Object> appsList = new ArrayList<Object> ();

	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl("https://api.trackvia.com/apps", clientId, responseTypes);
		builder.put("access_token", this.accessToken);
		
		return builder;
	}

	@Override
	protected void postExecute(String responseBody) {
	}
	
	public AppsStrategy(String accessToken) {
		setAccessToken(accessToken);
	}

	public List<Object> getAppsList() {
		return appsList;
	}

	private void setAppsList(List<Object> appsList) {
		this.appsList = appsList;
	}

}
