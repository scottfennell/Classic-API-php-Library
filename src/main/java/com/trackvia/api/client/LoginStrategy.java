package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class LoginStrategy extends APIStrategy {
	
	private String username = new String();
	private String password = new String();
	protected String clientSecret = new String();
	private String refreshToken = new String();
	
	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl("https://api.trackvia.com/oauth/v2/token", clientId, responseTypes);
		builder.put("client_secret", getClientSecret());
		
			
		Map<String, String> params = new HashMap<String, String> ();
		params.put("grant_type", "password");
		params.put("username", username);
		params.put("password", password);
		builder.putAll(params);
		
		return builder;
	}
	
	protected String getClientSecret() {
		return clientSecret;
	}

	protected void setClientSecret(String clientSecret) {
		this.clientSecret = clientSecret;
	}

	public LoginStrategy(String username, String password, String clientSecret) {
		this.username= username;
		this.password= password;
		this.clientSecret= clientSecret;
	}

	protected void postExecute(String responseBody) {
		Map<String, Object> responseMap = APIClient.getResponseAsMap(responseBody);
		
		setAccessToken((String)responseMap.get("access_token"));
		setRefreshToken((String)responseMap.get("refresh_token"));
		
	}
	
	public String getRefreshToken() {
		return refreshToken;
	}

	protected void setRefreshToken(String refreshToken) {
		this.refreshToken = refreshToken;
	}
	

}
