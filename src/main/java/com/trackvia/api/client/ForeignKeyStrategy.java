package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class ForeignKeyStrategy extends APIStrategy {
	
	private Long tableId = new Long(0);
	private Long foreignKeyId = new Long(0);

	@Override
	protected AuthorizationRequestUrl getRequestBuilder(String clientId) {
		
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder =  new AuthorizationRequestUrl("https://api.trackvia.com/tables/" + this.getTableId().toString() + "/foreign_keys/" + this.getForeignKeyId().toString() + ".json", clientId, responseTypes);
		builder.put("access_token", getAccessToken());
		
		return builder;		
	}

	@Override
	protected void postExecute(String responseBody) {
		List<Object> map = APIClient.getResponseAsList(responseBody);
	}
	
	public ForeignKeyStrategy(String accessToken, Long tableId, Long foreignKeyId) {
		setAccessToken(accessToken);
		setTableId(tableId);
		setForeignKeyId(foreignKeyId);
		
	}
	
	public Long getTableId() {
		return tableId;
	}

	private void setTableId(Long tableId) {
		this.tableId = tableId;
	}
	
	private void setForeignKeyId(Long ForeignKeyId) {
		this.foreignKeyId = ForeignKeyId;
	}
	
	public Long getForeignKeyId() {
		return this.foreignKeyId;
	}

}
